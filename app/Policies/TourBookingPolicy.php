<?php

namespace App\Policies;

use App\Models\TourBooking;
use App\Models\User;

class TourBookingPolicy
{
    public function view(User $user, TourBooking $booking): bool
    {
        return $user->id === $booking->user_id || $user->role === 'admin';
    }

    public function update(User $user, TourBooking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    public function cancel(User $user, TourBooking $booking): bool
    {
        return $user->id === $booking->user_id || $user->role === 'admin';
    }
}