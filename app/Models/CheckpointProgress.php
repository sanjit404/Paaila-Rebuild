<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class CheckpointProgress extends Model
{
    protected $fillable = [
        'tour_booking_id',
        'checkpoint_id',
        'reached_at',
        'facts_viewed',
        'distance_from_checkpoint',
    ];

    protected $casts = [
        'reached_at'               => 'datetime',
        'facts_viewed'             => 'boolean',
        'distance_from_checkpoint' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function tourBooking(): BelongsTo
    {
        return $this->belongsTo(TourBooking::class);
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function isReached(): bool
    {
        return ! is_null($this->reached_at);
    }

    public function markAsReached(): void
    {
        if (! $this->isReached()) {
            $this->update(['reached_at' => now()]);

            Log::info('Checkpoint reached', [
                'checkpoint_id' => $this->checkpoint_id,
                'booking_id'    => $this->tour_booking_id,
            ]);
        }
    }

    public function markFactsViewed(): void
    {
        $this->update(['facts_viewed' => true]);
    }

    // ─── Computed ─────────────────────────────────────────────────

    /**
     * Human-readable distance string.
     */
    public function getFormattedDistanceAttribute(): string
    {
        if (is_null($this->distance_from_checkpoint)) {
            return 'Unknown';
        }

        $d = (float) $this->distance_from_checkpoint;

        return $d < 1000
            ? round($d) . ' m'
            : round($d / 1000, 1) . ' km';
    }
}
