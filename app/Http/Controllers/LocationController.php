<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationHistory;
use App\Models\Geofence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index()
    {
        return view('map');
    }

    public function all()
    {
        $locations = Location::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($locations);
    }

    public function publicLocations()
    {
        $locations = Location::with('user:id,name')
            ->where('is_public', true)
            ->get();

        return response()->json($locations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'type' => 'nullable|string|in:marker,checkpoint,danger,favorite',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        $location = Location::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'user_id' => auth()->id(),
            'type' => $validated['type'] ?? 'marker',
            'icon' => $validated['icon'] ?? null,
            'color' => $validated['color'] ?? '#3388ff',
            'is_public' => $validated['is_public'] ?? false
        ]);

        return response()->json($location, 201);
    }

    public function update(Request $request, Location $location)
    {
        $this->authorize('update', $location);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'lat' => 'sometimes|numeric',
            'lng' => 'sometimes|numeric',
            'type' => 'sometimes|string|in:marker,checkpoint,danger,favorite',
            'color' => 'sometimes|string',
            'is_public' => 'sometimes|boolean'
        ]);

        $location->update($validated);

        return response()->json($location);
    }

    public function destroy(Location $location)
    {
        $this->authorize('delete', $location);
        $location->delete();

        return response()->json(['message' => 'Location deleted']);
    }

    public function live(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'altitude' => 'nullable|numeric',
            'heading' => 'nullable|numeric'
        ]);

        auth()->user()->update([
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'last_location_update' => now()
        ]);

        LocationHistory::create([
            'user_id' => auth()->id(),
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'accuracy' => $validated['accuracy'] ?? null,
            'speed' => $validated['speed'] ?? null,
            'altitude' => $validated['altitude'] ?? null,
            'heading' => $validated['heading'] ?? null
        ]);

        $geofences = Geofence::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();

        $triggeredGeofences = [];
        foreach ($geofences as $geofence) {
            if ($geofence->isPointInside($validated['lat'], $validated['lng'])) {
                $triggeredGeofences[] = [
                    'id' => $geofence->id,
                    'name' => $geofence->name,
                    'type' => 'inside'
                ];
            }
        }

        return response()->json([
            'ok' => true,
            'geofences' => $triggeredGeofences
        ]);
    }

    public function history(Request $request)
    {
        $minutes = $request->get('minutes', 60);
        
        $history = LocationHistory::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($history);
    }

    public function liveUsers()
    {
        $users = DB::table('users')
            ->select('id', 'name', 'lat', 'lng', 'last_location_update')
            ->where('sharing_enabled', true)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->where('id', '!=', auth()->id())
            ->where('last_location_update', '>=', now()->subMinutes(5))
            ->get();

        return response()->json($users);
    }

    public function getGeofences()
    {
        $geofences = Geofence::where('user_id', auth()->id())->get();
        return response()->json($geofences);
    }

    public function storeGeofence(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'center_lat' => 'required|numeric',
            'center_lng' => 'required|numeric',
            'radius' => 'required|integer|min:10|max:10000',
            'notify_entry' => 'boolean',
            'notify_exit' => 'boolean'
        ]);

        $geofence = Geofence::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'center_lat' => $validated['center_lat'],
            'center_lng' => $validated['center_lng'],
            'radius' => $validated['radius'],
            'notify_entry' => $validated['notify_entry'] ?? true,
            'notify_exit' => $validated['notify_exit'] ?? true,
            'is_active' => true
        ]);

        return response()->json($geofence, 201);
    }

    public function destroyGeofence(Geofence $geofence)
    {
        $this->authorize('delete', $geofence);
        $geofence->delete();

        return response()->json(['message' => 'Geofence deleted']);
    }

    public function stats()
    {
        $totalDistance = 0;
        $history = LocationHistory::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'asc')
            ->get();

        for ($i = 1; $i < count($history); $i++) {
            $prev = $history[$i - 1];
            $curr = $history[$i];
            
            $totalDistance += $this->calculateDistance(
                $prev->lat, $prev->lng,
                $curr->lat, $curr->lng
            );
        }

        $stats = [
            'total_locations' => Location::where('user_id', auth()->id())->count(),
            'public_locations' => Location::where('user_id', auth()->id())->where('is_public', true)->count(),
            'distance_today' => round($totalDistance / 1000, 2), // km
            'active_geofences' => Geofence::where('user_id', auth()->id())->where('is_active', true)->count(),
            'history_points_today' => LocationHistory::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->startOfDay())
                ->count()
        ];

        return response()->json($stats);
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // meters
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
