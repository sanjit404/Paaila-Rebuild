<?php

namespace App\Http\Controllers;

use App\Models\TourBooking;
use App\Models\TrackingPin;
use App\Models\Checkpoint;
use App\Models\CheckpointProgress;
use App\Models\TravelerLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TRAVELER - Active trekker GPS interface
    |--------------------------------------------------------------------------
    */

    public function traveler(TourBooking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($booking->status !== 'active') {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('error', 'Trek is not active. Please start it from your booking.');
        }

        $booking->load([
            'tourPackage.checkpoints.facts',
            'checkpointProgress.checkpoint',
            'trackingPin',
        ]);

        // Ensure a progress row exists for every checkpoint
        foreach ($booking->tourPackage->checkpoints as $cp) {
            CheckpointProgress::firstOrCreate([
                'tour_booking_id' => $booking->id,
                'checkpoint_id'   => $cp->id,
            ]);
        }

        $booking->load('checkpointProgress');

        return view('tracking.traveler', compact('booking'));
    }

    /*
    |--------------------------------------------------------------------------
    | PIN ENTRY - Public page for family / friends
    |--------------------------------------------------------------------------
    */

    public function pinEntry()
    {
        return view('tracking.pin-entry');
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFY PIN
    |--------------------------------------------------------------------------
    */

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6',
        ]);

        $trackingPin = TrackingPin::where('pin', $request->pin)
            ->where('expires_at', '>', now())
            ->first();

        if (! $trackingPin) {
            return back()
                ->withErrors(['pin' => 'Invalid or expired PIN. Please check and try again.'])
                ->withInput();
        }

        session(['tracking_pin' => $trackingPin->pin]);

        return redirect()->route('tracking.parent', $trackingPin->tourBooking);
    }

    /*
    |--------------------------------------------------------------------------
    | PARENT VIEW - Family / friends monitor trek
    |--------------------------------------------------------------------------
    */

    public function parent(TourBooking $booking)
    {
        $sessionPin = session('tracking_pin');

        if (
            ! $sessionPin ||
            ! $booking->trackingPin ||
            $booking->trackingPin->pin !== $sessionPin ||
            $booking->trackingPin->isExpired()
        ) {
            return redirect()
                ->route('tracking.pin.entry')
                ->with('error', 'Please enter a valid PIN to access tracking.');
        }

        $booking->load([
            'user',
            'tourPackage.checkpoints',
            'checkpointProgress.checkpoint',
            'trackingPin',
        ]);

        return view('tracking.parent', compact('booking'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE LOCATION - Called every 5 s from traveler device
    |
    | Algorithm:
    | 1. Save GPS coordinates to traveler_locations
    | 2. For each checkpoint, compute Haversine distance
    | 3. If distance <= detection_radius → geofence triggered → mark reached
    | 4. Return checkpoint data + progress to frontend
    |--------------------------------------------------------------------------
    */

    public function updateLocation(Request $request, TourBooking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'accuracy'      => 'nullable|numeric|min:0',
            'speed'         => 'nullable|numeric|min:0',
            'altitude'      => 'nullable|numeric',
            'heading'       => 'nullable|numeric|between:0,360',
            'battery_level' => 'nullable|string|max:10',
        ]);

        try {
            DB::beginTransaction();

            // 1. Persist GPS location
            $location = TravelerLocation::create([
                'tour_booking_id' => $booking->id,
                'latitude'        => $validated['latitude'],
                'longitude'       => $validated['longitude'],
                'accuracy'        => $validated['accuracy']      ?? null,
                'speed'           => $validated['speed']         ?? null,
                'altitude'        => $validated['altitude']      ?? null,
                'heading'         => $validated['heading']       ?? null,
                'battery_level'   => $validated['battery_level'] ?? null,
            ]);

            // 2. Geofencing — check every checkpoint
            $checkpointReached = null;
            $nextCheckpoint    = null;
            $distanceToNext    = null;

            $checkpoints = $booking->tourPackage
                ->checkpoints()
                ->orderBy('order')
                ->get();

            foreach ($checkpoints as $checkpoint) {
                $progress = CheckpointProgress::firstOrCreate([
                    'tour_booking_id' => $booking->id,
                    'checkpoint_id'   => $checkpoint->id,
                ]);

                // --- HAVERSINE DISTANCE ---
                $distance = $this->haversine(
                    $validated['latitude'],
                    $validated['longitude'],
                    (float) $checkpoint->latitude,
                    (float) $checkpoint->longitude
                );

                // Persist real-time distance
                $progress->update(['distance_from_checkpoint' => round($distance, 2)]);

                // --- GEOFENCE BOUNDARY CHECK ---
                $radius = (int) ($checkpoint->detection_radius ?? 50);

                if ($distance <= $radius && ! $progress->reached_at) {
                    $progress->update(['reached_at' => now()]);
                    $checkpointReached = $checkpoint->load('facts');

                    Log::info('Geofence entry', [
                        'booking'    => $booking->booking_number,
                        'checkpoint' => $checkpoint->name,
                        'distance'   => round($distance, 1) . 'm',
                        'radius'     => $radius . 'm',
                    ]);
                }

                // First unreached checkpoint = "next"
                if (! $nextCheckpoint && ! $progress->reached_at) {
                    $nextCheckpoint = $checkpoint;
                    $distanceToNext = round($distance);
                }
            }

            $booking->load('checkpointProgress');

            DB::commit();

            return response()->json([
                'success'               => true,
                'checkpoint_reached'    => (bool) $checkpointReached,
                'checkpoint'            => $checkpointReached
                    ? $this->formatCheckpoint($checkpointReached)
                    : null,
                'next_checkpoint'       => $nextCheckpoint ? [
                    'id'          => $nextCheckpoint->id,
                    'name'        => $nextCheckpoint->name,
                    'description' => $nextCheckpoint->description,
                    'order'       => $nextCheckpoint->order,
                ] : null,
                'distance_to_next'      => $distanceToNext,
                'progress'              => $booking->progress_percentage,
                'completed_checkpoints' => $booking->completed_checkpoints,
                'total_checkpoints'     => $booking->total_checkpoints,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Location update failed', [
                'booking_id' => $booking->id,
                'error'      => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to update location.'], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET CURRENT LOCATION - Polled every 5 s by parent view
    |--------------------------------------------------------------------------
    */

    public function getCurrentLocation(TourBooking $booking)
    {
        $sessionPin = session('tracking_pin');

        if (
            ! $sessionPin ||
            ! $booking->trackingPin ||
            $booking->trackingPin->pin !== $sessionPin
        ) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $location = $booking->travelerLocations()->latest()->first();

        if (! $location) {
            return response()->json(['error' => 'No location data yet.'], 404);
        }

        $secondsAgo = $location->created_at->diffInSeconds(now());

        [$statusClass, $onlineStatus] = match(true) {
            $secondsAgo < 30  => ['online',  'Online now'],
            $secondsAgo < 300 => ['recent',  'Last seen ' . $location->created_at->diffForHumans()],
            default           => ['offline', 'Offline — ' . $location->created_at->diffForHumans()],
        };

        $booking->load('checkpointProgress');

        $progress = $booking->checkpointProgress()
            ->with('checkpoint:id,name,order')
            ->get()
            ->map(fn ($p) => [
                'checkpoint_id'   => $p->checkpoint_id,
                'checkpoint_name' => $p->checkpoint->name ?? '',
                'reached'         => (bool) $p->reached_at,
                'reached_at'      => $p->reached_at?->toIso8601String(),
                'distance'        => $p->distance_from_checkpoint,
            ]);

        return response()->json([
            'location' => [
                'latitude'      => (float) $location->latitude,
                'longitude'     => (float) $location->longitude,
                'accuracy'      => $location->accuracy      ? (float) $location->accuracy      : null,
                'speed'         => $location->speed         ? (float) $location->speed         : null,
                'altitude'      => $location->altitude      ? (float) $location->altitude      : null,
                'heading'       => $location->heading       ? (float) $location->heading       : null,
                'battery_level' => $location->battery_level,
                'updated_at'    => $location->created_at->toIso8601String(),
            ],
            'status_class'          => $statusClass,
            'online_status'         => $onlineStatus,
            'progress'              => $progress,
            'completed_checkpoints' => $booking->completed_checkpoints,
            'total_checkpoints'     => $booking->total_checkpoints,
            'progress_percentage'   => $booking->progress_percentage,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MARK FACTS VIEWED
    |--------------------------------------------------------------------------
    */

    public function markFactsViewed(TourBooking $booking, Checkpoint $checkpoint)
    {
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        CheckpointProgress::where('tour_booking_id', $booking->id)
            ->where('checkpoint_id', $checkpoint->id)
            ->update(['facts_viewed' => true]);

        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | HAVERSINE FORMULA
    |
    |   a = sin²(Δφ/2) + cos(φ1)·cos(φ2)·sin²(Δλ/2)
    |   c = 2·atan2(√a, √(1−a))
    |   d = R·c          (R = 6 371 000 m)
    |
    | Returns distance in metres between two WGS-84 coordinates.
    |--------------------------------------------------------------------------
    */

    private function haversine(
        float $lat1, float $lon1,
        float $lat2, float $lon2
    ): float {
        $R  = 6_371_000; // Earth mean radius in metres
        $φ1 = deg2rad($lat1);
        $φ2 = deg2rad($lat2);
        $Δφ = deg2rad($lat2 - $lat1);
        $Δλ = deg2rad($lon2 - $lon1);

        $a = sin($Δφ / 2) ** 2
           + cos($φ1) * cos($φ2) * sin($Δλ / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT CHECKPOINT for JSON response
    |--------------------------------------------------------------------------
    */

    private function formatCheckpoint(Checkpoint $checkpoint): array
    {
        return [
            'id'                => $checkpoint->id,
            'name'              => $checkpoint->name,
            'description'       => $checkpoint->description,
            'description'       => $checkpoint->description,
            'latitude'          => (float) $checkpoint->latitude,
            'longitude'         => (float) $checkpoint->longitude,
            'order'             => $checkpoint->order,
            'facts'             => $checkpoint->facts->map(fn ($f) => [
                'id'         => $f->id,
                'title'      => $f->title,
                'content'    => $f->content,
                'type'       => $f->type       ?? 'info',
                'icon_class' => $f->icon_class ?? 'fas fa-info-circle',
            ])->values()->toArray(),
        ];
    }
}
