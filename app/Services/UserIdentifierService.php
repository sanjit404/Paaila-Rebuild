<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class UserIdentifierService
{
    /**
     * Get unique identifier for current user/device
     */
    public static function getIdentifier(): string
    {
        // If user is logged in, use user_id
        if (Auth::check()) {
            return 'user_' . Auth::id();
        }

        // Otherwise, generate device-based identifier
        return self::getDeviceIdentifier();
    }

    /**
     * Generate device identifier from session/IP/UA
     */
    private static function getDeviceIdentifier(): string
    {
        $sessionId = session()->getId();
        $ip = request()->ip();
        $userAgent = request()->userAgent();

        // Create hash from session + IP + UA
        $raw = $sessionId . $ip . $userAgent;
        
        return 'device_' . hash('sha256', $raw);
    }
}