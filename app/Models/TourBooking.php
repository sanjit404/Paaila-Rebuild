<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class TourBooking extends Model
{
   
    
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    
    
    protected $fillable = [
        'user_id',
        'tour_package_id',
        'booking_number',
        'tour_date',
        'participants',
        'total_amount',
        'payment_method',
        'status',
        'confirmed_at',
        'paid_at',
        'started_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'tour_date' => 'date',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

   
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function trackingPin(): HasOne
    {
        return $this->hasOne(TrackingPin::class);
    }

    public function checkpointProgress(): HasMany
    {
        return $this->hasMany(CheckpointProgress::class);
    }

    public function travelerLocations(): HasMany
    {
        return $this->hasMany(TravelerLocation::class);
    }

    
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = 'TRK' . str_pad((self::max('id') + 1), 6, '0', STR_PAD_LEFT);
            }
        });
    }

   
    
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    
    public function isInActiveState(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_ACTIVE,
        ]);
    }

   
    
    
    public function markAsConfirmed(): bool
    {
        if (!$this->isPending()) {
            Log::warning('Cannot confirm booking - not in pending state', [
                'booking_id' => $this->id,
                'current_status' => $this->status,
            ]);
            return false;
        }

        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
            'paid_at' => now(),
        ]);

        Log::info('Booking confirmed', ['booking_id' => $this->id]);
        
        return true;
    }

   
    public function markAsActive(): bool
    {
        if (!$this->isConfirmed()) {
            Log::warning('Cannot activate booking - not confirmed', [
                'booking_id' => $this->id,
                'current_status' => $this->status,
            ]);
            return false;
        }

        $this->update([
            'status' => self::STATUS_ACTIVE,
            'started_at' => now(),
        ]);

        Log::info('Booking activated', ['booking_id' => $this->id]);
        
        return true;
    }

    
    public function markAsCompleted(bool $adminVerified = false): bool
    {
        if ($this->isCompleted()) {
            Log::info('Booking already completed', ['booking_id' => $this->id]);
            return true;
        }

        if ($this->isCancelled()) {
            Log::warning('Cannot complete cancelled booking', ['booking_id' => $this->id]);
            return false;
        }

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        Log::info('Booking completed', [
            'booking_id' => $this->id,
            'admin_verified' => $adminVerified,
        ]);
        
        return true;
    }

    
    public function markAsCancelled(): bool
    {
        if ($this->isCompleted()) {
            Log::warning('Cannot cancel completed booking', ['booking_id' => $this->id]);
            return false;
        }

        if ($this->isCancelled()) {
            Log::info('Booking already cancelled', ['booking_id' => $this->id]);
            return true;
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);

        Log::info('Booking cancelled', ['booking_id' => $this->id]);
        
        return true;
    }

    
    
    public function getCompletedCheckpointsAttribute(): int
    {
        return $this->checkpointProgress()->whereNotNull('reached_at')->count();
    }

    public function getTotalCheckpointsAttribute(): int
    {
        return $this->tourPackage->checkpoints()->count();
    }

    public function getProgressPercentageAttribute(): int
    {
        $total = $this->total_checkpoints;
        
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->completed_checkpoints / $total) * 100);
    }

    public function getNextCheckpointAttribute()
    {
        $completedIds = $this->checkpointProgress()
            ->whereNotNull('reached_at')
            ->pluck('checkpoint_id')
            ->toArray();

        return $this->tourPackage->checkpoints()
            ->whereNotIn('id', $completedIds)
            ->orderBy('order')
            ->first();
    }

    
    public function shouldBeCompleted(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $endDate = $this->tour_date->copy()->addDays($this->tourPackage->duration_days);
        
        return now()->greaterThan($endDate);
    }

    
    public function allCheckpointsReached(): bool
    {
        return $this->progress_percentage === 100;
    }

    
    public function hasReachedCheckpoint($checkpointId): bool
    {
        return $this->checkpointProgress()
            ->where('checkpoint_id', $checkpointId)
            ->whereNotNull('reached_at')
            ->exists();
    }

    
    
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_ACTIVE,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }
}