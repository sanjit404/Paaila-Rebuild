<?php

namespace App\Http\Controllers;

use App\Models\TrekRating;
use App\Models\TourBooking;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrekRatingController extends Controller
{
    
    public function store(Request $request, TourBooking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $result = TrekRating::writeOnce(
            packageId: $booking->tour_package_id,
            userId:    auth()->id(),
            bookingId: $booking->id,
            stars:     $validated['rating'],
            review:    $validated['review'] ?? null,
        );

        if (!$result['success']) {
            return back()->with('error', $result['reason']);
        }

        RecommendationService::bust(auth()->id());
        RecommendationService::bustPopular();
        RecommendationService::bustSimilar($booking->tour_package_id);

        return back()->with(
            'success',
            'Thank you for your ' . $result['rating']->rating . '-star rating! It helps other trekkers.'
        );
    }
}
