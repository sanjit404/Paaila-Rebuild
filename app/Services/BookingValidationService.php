<?php

namespace App\Services;

use App\Models\User;
use App\Models\TourBooking;
use Illuminate\Validation\ValidationException;

class BookingValidationService
{
    
    public static function validateCanBook(User $user): void
    {
        $activeBooking = TourBooking::where('user_id', $user->id)
            ->active()
            ->first();

        if ($activeBooking) {
            throw ValidationException::withMessages([
                'booking' => sprintf(
                    'You already have an active booking (#%d - %s). Please complete or cancel it before creating a new one.',
                    $activeBooking->id,
                    $activeBooking->tourPackage->name
                ),
            ]);
        }
    }

   
    public static function autoCompleteExpiredBookings(): int
    {
        $completedCount = 0;

        $activeBookings = TourBooking::where('status', TourBooking::STATUS_ACTIVE)->get();

        foreach ($activeBookings as $booking) {
            if ($booking->shouldBeCompleted()) {
                $booking->markAsCompleted(false); // Not admin-verified
                $completedCount++;
            }
        }

        return $completedCount;
    }
}