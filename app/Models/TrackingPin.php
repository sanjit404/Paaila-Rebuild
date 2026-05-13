<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingPin extends Model
{
    protected $fillable = [
        'tour_booking_id',
        'pin',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function tourBooking(): BelongsTo
    {
        return $this->belongsTo(TourBooking::class);
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function isValid(): bool
    {
        return ! $this->isExpired();
    }

    public static function findByPin(string $pin): ?self
    {
        return self::where('pin', $pin)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function recordAccess(): void
    {
        \Log::info('Tracking PIN accessed', [
            'pin'        => $this->pin,
            'booking_id' => $this->tour_booking_id,
            'ip'         => request()->ip(),
        ]);
    }
}
