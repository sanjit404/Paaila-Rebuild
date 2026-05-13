<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class UserIdentifierService
{
    
    public static function getIdentifier(): string
    {
        if (Auth::check()) {
            return 'user_' . Auth::id();
        }

        return self::getDeviceIdentifier();
    }

    private static function getDeviceIdentifier(): string
    {
        $sessionId = session()->getId();
        $ip = request()->ip();
        $userAgent = request()->userAgent();

        $raw = $sessionId . $ip . $userAgent;
        
        return 'device_' . hash('sha256', $raw);
    }
}