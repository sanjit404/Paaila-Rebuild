<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class TrekRating extends Model
{
    protected $fillable = [
        'tour_package_id',
        'user_id',
        'tour_booking_id',
        'rating',
        'review',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    protected static function booted(): void
    {
        static::created(fn($r) => self::recalculate($r->tour_package_id));

        static::updating(function ($r) {
            Log::warning('WORM violation: attempt to update trek rating blocked', [
                'rating_id'  => $r->id,
                'user_id'    => $r->user_id,
                'package_id' => $r->tour_package_id,
            ]);
            return false; 
        });
    }


    public static function writeOnce(
        int    $packageId,
        int    $userId,
        int    $bookingId,
        int    $stars,
        ?string $review = null
    ): array {

        $booking = TourBooking::where('id', $bookingId)
            ->where('user_id', $userId)
            ->where('tour_package_id', $packageId)
            ->where('status', 'completed')
            ->first();

        if (!$booking) {
            return [
                'success' => false,
                'reason'  => 'You can only rate treks you have completed.',
            ];
        }

        $existing = self::where('tour_package_id', $packageId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            return [
                'success'  => false,
                'reason'   => 'You have already rated this trek. Ratings cannot be changed.',
                'rated_at' => $existing->created_at->format('M d, Y'),
                'rating'   => $existing->rating,
            ];
        }

        if ($stars < 1 || $stars > 5) {
            return [
                'success' => false,
                'reason'  => 'Rating must be between 1 and 5 stars.',
            ];
        }

        $rating = self::create([
            'tour_package_id' => $packageId,
            'user_id'         => $userId,
            'tour_booking_id' => $bookingId,
            'rating'          => $stars,
            'review'          => $review ? strip_tags(trim($review)) : null,
        ]);

        Log::info('Trek rating written (WORM)', [
            'rating_id'  => $rating->id,
            'user_id'    => $userId,
            'package_id' => $packageId,
            'stars'      => $stars,
        ]);

        return [
            'success' => true,
            'rating'  => $rating,
        ];
    }


    public static function recalculate(int $packageId): void
    {
        $agg = self::where('tour_package_id', $packageId)
            ->selectRaw('ROUND(AVG(rating), 2) as avg_rating, COUNT(*) as total')
            ->first();

        TourPackage::where('id', $packageId)->update([
            'rating_avg'   => $agg->avg_rating ?? 0,
            'rating_count' => $agg->total      ?? 0,
        ]);
    }


    public static function hasRated(int $userId, int $packageId): bool
    {
        return self::where('user_id', $userId)
            ->where('tour_package_id', $packageId)
            ->exists();
    }

    public static function getRating(int $userId, int $packageId): ?self
    {
        return self::where('user_id', $userId)
            ->where('tour_package_id', $packageId)
            ->first();
    }


    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(TourBooking::class, 'tour_booking_id');
    }


    public function getStarLabelAttribute(): string
    {
        return match($this->rating) {
            5 => 'Outstanding',
            4 => 'Great',
            3 => 'Good',
            2 => 'Fair',
            1 => 'Poor',
            default => '',
        };
    }

    public function getLikedTypesAttribute(): array
    {
        if ($this->rating < 4) return [];

        $package = $this->tourPackage;
        if (!$package) return [];

        $types = [];
        if ($package->trek_type) $types[] = $package->trek_type;
        foreach ((array) ($package->tags ?? []) as $tag) $types[] = $tag;

        return array_unique($types);
    }
}
