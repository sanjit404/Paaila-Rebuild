<?php

namespace App\Policies;

use App\Models\TourBooking;
use App\Models\User;

class TourBookingPolicy
{
    /**
     * Determine if user can view the booking
     */
    public function view(User $user, TourBooking $booking): bool
    {
        return $user->id === $booking->user_id || $user->role === 'admin';
    }

    /**
     * Determine if user can update the booking
     */
    public function update(User $user, TourBooking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    /**
     * Determine if user can cancel the booking
     */
    public function cancel(User $user, TourBooking $booking): bool
    {
        return $user->id === $booking->user_id || $user->role === 'admin';
    }
}