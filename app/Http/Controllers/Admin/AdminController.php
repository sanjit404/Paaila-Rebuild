<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\Post;
use App\Models\TourBooking;
use App\Models\Checkpoint;
use App\Models\CheckpointFact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Mail\BookingCompleted;
use Illuminate\Support\Facades\Mail;

class AdminController extends BaseController
{
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use \Illuminate\Foundation\Validation\ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                abort(403, 'Unauthorized access. Admin only.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'total_packages' => TourPackage::count(),
            'active_packages' => TourPackage::where('is_active', true)->count(),
            'total_bookings' => TourBooking::count(),
            'pending_bookings' => TourBooking::where('status', 'pending')->count(),
            'active_tours' => TourBooking::where('status', 'active')->count(),
            'completed_tours' => TourBooking::where('status', 'completed')->count(),
            'total_revenue' => TourBooking::whereIn('status', ['paid', 'active', 'completed'])->sum('total_amount'),
            'monthly_revenue' => TourBooking::whereIn('status', ['paid', 'active', 'completed'])
                ->whereMonth('created_at', date('m'))
                ->sum('total_amount'),
        ];

        $recent_bookings = TourBooking::with(['user', 'tourPackage'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings'));
    }

    public function packages(Request $request)
    {
        $query = TourPackage::with(['checkpoints.facts', 'bookings']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('start_location_name', 'like', '%' . $request->search . '%')
                    ->orWhere('end_location_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        $packages = $query->latest()->paginate(10);

        return view('admin.packages.index', compact('packages'));
    }

    public function createPackage()
    {
        return view('admin.packages.create');
    }

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'trek_type' => 'nullable|string|max:50',
            'tags' => 'nullable|string',
            'season' => 'nullable|array',
            'region' => 'nullable|string|max:100',
            'views_count' => 'nullable|integer|min:0',
            'bookings_count' => 'nullable|integer|min:0',
            'rating_avg' => 'nullable|numeric|min:0|max:5',
            'rating_count' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'difficulty_level' => 'required|in:easy,moderate,hard',
            'max_participants' => 'required|integer|min:1',
            'image' => 'nullable|string',
            'start_location_name' => 'required|string|max:255',
            'start_lat' => 'required|numeric',
            'start_lng' => 'required|numeric',
            'end_location_name' => 'required|string|max:255',
            'end_lat' => 'required|numeric',
            'end_lng' => 'required|numeric',
            'is_active' => 'nullable|boolean',
        ]);

        // Replace the tags line in both methods:
        $validated['tags'] = null;
        if ($request->filled('tags')) {
            $raw = $request->input('tags');
            // Handle JSON array (from tag input widgets) or plain comma-separated string
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $validated['tags'] = array_values(array_filter($decoded));
            } else {
                $validated['tags'] = array_values(array_filter(array_map('trim', explode(',', $raw))));
            }
        }

        $validated['season'] = $request->filled('season') ? $request->season : null;
        $validated['views_count'] = $validated['views_count'] ?? 0;
        $validated['bookings_count'] = $validated['bookings_count'] ?? 0;
        $validated['rating_avg'] = $validated['rating_avg'] ?? 0.00;
        $validated['rating_count'] = $validated['rating_count'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        $package = TourPackage::create($validated);

        return redirect()->route('admin.packages.edit', $package)
            ->with('success', 'Package created! Now add checkpoints.');
    }

    public function editPackage(TourPackage $package)
    {
        $package->load(['checkpoints.facts']);
        return view('admin.packages.edit', compact('package'));
    }

    public function updatePackage(Request $request, TourPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'trek_type' => 'nullable|string|max:50',
            'tags' => 'nullable|string',
            'season' => 'nullable|array',
            'region' => 'nullable|string|max:100',
            'views_count' => 'nullable|integer|min:0',
            'bookings_count' => 'nullable|integer|min:0',
            'rating_avg' => 'nullable|numeric|min:0|max:5',
            'rating_count' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'difficulty_level' => 'required|in:easy,moderate,hard',
            'max_participants' => 'required|integer|min:1',
            'image' => 'nullable|string',
            'start_location_name' => 'required|string|max:255',
            'start_lat' => 'required|numeric',
            'start_lng' => 'required|numeric',
            'end_location_name' => 'required|string|max:255',
            'end_lat' => 'required|numeric',
            'end_lng' => 'required|numeric',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['tags'] = $request->filled('tags')
            ? array_values(array_filter(array_map('trim', explode(',', $request->tags))))
            : null;

        $validated['season'] = $request->filled('season') ? $request->season : null;
        $validated['is_active'] = $request->boolean('is_active');

        $package->update($validated);

        return back()->with('success', 'Package updated successfully!');
    }

    public function deletePackage(TourPackage $package)
    {
        if ($package->bookings()->count() > 0) {
            return back()->with('error', 'Cannot delete package with existing bookings.');
        }

        $package->delete();

        return redirect()->route('admin.packages')
            ->with('success', 'Package deleted successfully!');
    }

    public function togglePackageStatus(TourPackage $package)
    {
        $package->update(['is_active' => !$package->is_active]);

        return redirect()->back()->with('success', 'Package status updated');
    }

    public function duplicatePackage(TourPackage $package)
    {
        $package->load(['checkpoints.facts']);

        $newPackage = $package->replicate();
        $newPackage->name = $package->name . ' (Copy)';
        $newPackage->is_active = false;
        $newPackage->save();

        foreach ($package->checkpoints as $checkpoint) {
            $newCheckpoint = $checkpoint->replicate();
            $newCheckpoint->tour_package_id = $newPackage->id;
            $newCheckpoint->save();

            foreach ($checkpoint->facts as $fact) {
                $newFact = $fact->replicate();
                $newFact->checkpoint_id = $newCheckpoint->id;
                $newFact->save();
            }
        }

        return response()->json([
            'success' => true,
            'package_id' => $newPackage->id
        ]);
    }

    public function addCheckpoint(Request $request, TourPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|integer|min:10',
            'estimated_time_from_previous' => 'nullable|string|min:0',
        ]);

        $validated['order'] = ($package->checkpoints()->max('order') ?? 0) + 1;
        $validated['radius'] = $validated['radius'] ?? 100;
        $validated['tour_package_id'] = $package->id;

        $package->checkpoints()->create($validated);

        return back()->with('success', 'Checkpoint added successfully!');
    }

    public function updateCheckpoint(Request $request, Checkpoint $checkpoint)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|integer|min:10',
            'estimated_time_from_previous' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
        ]);

        $checkpoint->update($validated);

        return back()->with('success', 'Checkpoint updated successfully!');
    }

    public function deleteCheckpoint(Checkpoint $checkpoint)
    {
        $checkpoint->delete();

        return back()->with('success', 'Checkpoint deleted successfully!');
    }

    public function addFact(Request $request, Checkpoint $checkpoint)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:history,culture,safety,info',
        ]);

        $validated['order'] = ($checkpoint->facts()->max('order') ?? 0) + 1;

        $checkpoint->facts()->create($validated);

        return back()->with('success', 'Fact added successfully!');
    }

    public function updateFact(Request $request, CheckpointFact $fact)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:history,culture,safety,info',
            'order' => 'nullable|integer|min:1',
        ]);

        $fact->update($validated);

        return back()->with('success', 'Fact updated successfully!');
    }

    public function deleteFact(CheckpointFact $fact)
    {
        $fact->delete();

        return back()->with('success', 'Fact deleted successfully!');
    }

    public function confirmBooking(TourBooking $booking)
    {
        Log::info('Admin confirming booking', [
            'booking_id' => $booking->id,
            'current_status' => $booking->status,
            'admin_id' => auth()->id(),
        ]);

        if (!$booking->isPending()) {
            return back()->with('error', 'Only pending bookings can be confirmed.');
        }

        if ($booking->markAsConfirmed()) {
            return back()->with('success', 'Booking confirmed successfully!');
        }

        return back()->with('error', 'Failed to confirm booking.');
    }

    public function completeBooking(TourBooking $booking)
    {
        Log::info('Admin completing booking', [
            'booking_id' => $booking->id,
            'current_status' => $booking->status,
            'progress' => $booking->progress_percentage,
            'admin_id' => auth()->id(),
        ]);

        if ($booking->isCompleted()) {
            return back()->with('info', 'Booking is already completed.');
        }

        if ($booking->isCancelled()) {
            return back()->with('error', 'Cancelled bookings cannot be completed.');
        }
        $booking->load('trackingPin');
        $booking->load('user');
        
        if ($booking->markAsCompleted(true)) {
            Log::info('Sending completion email', [
                'booking_id' => $booking->id
            ]);
            try {
                Mail::to($booking->user->email)->send(new BookingCompleted($booking));
            } catch (\Exception $e) {
                Log::warning('Booking completed email failed', [
                    'booking_id' => $booking->id,
                    'error'      => $e->getMessage(),
                ]);
            }
            return back()->with('success', 'Booking marked as completed!');
        }

        return back()->with('error', 'Failed to complete booking.');
    }

    public function cancelBooking(Request $request, TourBooking $booking)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        Log::info('Admin cancelling booking', [
            'booking_id' => $booking->id,
            'reason' => $request->input('reason'),
            'admin_id' => auth()->id(),
        ]);

        if ($booking->isCompleted()) {
            return back()->with('error', 'Completed bookings cannot be cancelled.');
        }

        if ($booking->markAsCancelled()) {
            return back()->with('success', 'Booking cancelled successfully!');
        }

        return back()->with('error', 'Failed to cancel booking.');
    }

    public function viewBooking(TourBooking $booking)
    {
        $booking->load([
            'user',
            'tourPackage.checkpoints.facts',
            'checkpointProgress.checkpoint',
            'trackingPin',
            'travelerLocations' => function ($query) {
                $query->latest()->limit(10);
            }
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    public function bookings(Request $request)
    {
        $query = TourBooking::with(['user', 'tourPackage']);

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportBookingsCSV($query->get());
        }

        $bookings = $query->latest()->paginate(20);

        $stats = [
            'total' => TourBooking::count(),
            'pending' => TourBooking::where('status', 'pending')->count(),
            'paid' => TourBooking::where('status', 'paid')->count(),
            'active' => TourBooking::where('status', 'active')->count(),
            'completed' => TourBooking::where('status', 'completed')->count(),
        ];

        return view('admin.bookings', compact('bookings', 'stats'));
    }

    public function markBookingPaid(TourBooking $booking)
    {
        $booking->update(['status' => 'paid']);

        return response()->json(['success' => true]);
    }

    private function exportBookingsCSV($bookings)
    {
        $filename = 'bookings_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'Customer', 'Email', 'Trek', 'Date', 'Trekkers', 'Amount', 'Status', 'Booked At']);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->id,
                    $booking->user->name,
                    $booking->user->email,
                    $booking->tourPackage->name,
                    $booking->tour_date->format('Y-m-d'),
                    $booking->participants,
                    $booking->total_amount,
                    $booking->status,
                    $booking->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function Posts(Request $request)
    {
        $query = Post::with('trek');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $posts = $query->latest()->paginate(15);

        $stats = [
            'total' => Post::count(),
            'news' => Post::where('type', 'news')->count(),
            'offers' => Post::where('type', 'offer')->count(),
            'treks' => Post::where('type', 'trek')->count(),
            'highlighted' => Post::where('is_highlighted', true)->count(),
        ];

        return view('admin.posts.index', compact('posts', 'stats'));
    }

    public function createPost()
    {
        $trekPackages = TourPackage::where('is_active', true)->get();

        return view('admin.posts.create', compact('trekPackages'));
    }

    public function storePost(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:news,offer,trek',
            'image' => 'nullable|string',
            'is_highlighted' => 'boolean',
            'trek_id' => 'nullable|exists:tour_packages,id',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('posts', 'public');
                $validated['image'] = '/storage/' . $imagePath;
            }

            if ($request->boolean('is_highlighted')) {
                Post::where('is_highlighted', true)->update(['is_highlighted' => false]);
            }

            $post = Post::create($validated);

            DB::commit();

            Log::info('Post created', [
                'post_id' => $post->id,
                'admin_id' => auth()->id(),
            ]);

            return redirect()->route('admin.posts')->with('success', 'Post created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Post creation failed', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to create post.')->withInput();
        }
    }

    public function editPost(Post $post)
    {
        $trekPackages = TourPackage::where('is_active', true)->get();

        return view('admin.posts.edit', compact('post', 'trekPackages'));
    }

    public function updatePost(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:news,offer,trek',
            'image' => 'nullable|string',
            'is_highlighted' => 'boolean',
            'trek_id' => 'nullable|exists:tour_packages,id',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                if ($post->image && file_exists(public_path($post->image))) {
                    unlink(public_path($post->image));
                }

                $imagePath = $request->file('image')->store('posts', 'public');
                $validated['image'] = '/storage/' . $imagePath;
            }

            if ($request->boolean('is_highlighted')) {
                Post::where('id', '!=', $post->id)
                    ->where('is_highlighted', true)
                    ->update(['is_highlighted' => false]);
            }

            $post->update($validated);

            DB::commit();

            return back()->with('success', 'Post updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Post update failed', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update post.')->withInput();
        }
    }

    public function deletePost(Post $post)
    {
        try {
            if ($post->image && file_exists(public_path($post->image))) {
                unlink(public_path($post->image));
            }

            $post->delete();

            Log::info('Post deleted', [
                'post_id' => $post->id,
                'admin_id' => auth()->id(),
            ]);

            return redirect()->route('admin.posts')->with('success', 'Post deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Post deletion failed', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete post.');
        }
    }
}