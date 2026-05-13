<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelerLocation extends Model
{
    protected $fillable = [
        'tour_booking_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'altitude',
        'heading',
        'battery_level',
    ];

    protected $casts = [
        'latitude'  => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy'  => 'decimal:2',
        'speed'     => 'decimal:2',
        'altitude'  => 'decimal:2',
        'heading'   => 'decimal:2',
    ];


    public function tourBooking(): BelongsTo
    {
        return $this->belongsTo(TourBooking::class);
    }


    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getStatusClassAttribute(): string
    {
        $seconds = $this->created_at->diffInSeconds(now());

        return match(true) {
            $seconds < 30  => 'online',
            $seconds < 300 => 'recent',
            default        => 'offline',
        };
    }

    public function isOnline(): bool
    {
        return $this->created_at->diffInSeconds(now()) < 30;
    }

    public function distanceTo(float $lat, float $lon): float
    {
        $R  = 6_371_000;
        $φ1 = deg2rad((float) $this->latitude);
        $φ2 = deg2rad($lat);
        $Δφ = deg2rad($lat - (float) $this->latitude);
        $Δλ = deg2rad($lon - (float) $this->longitude);

        $a = sin($Δφ / 2) ** 2 + cos($φ1) * cos($φ2) * sin($Δλ / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }
}
