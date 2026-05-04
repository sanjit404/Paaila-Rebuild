<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checkpoint extends Model
{
    protected $fillable = [
        'tour_package_id',
        'name',
        'description',
        'latitude',
        'longitude',
        'order',
        'detection_radius',  // geofence radius in metres (default 50)
    ];

    protected $casts = [
        'latitude'         => 'decimal:8',
        'longitude'        => 'decimal:8',
        'order'            => 'integer',
        'detection_radius' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function facts(): HasMany
    {
        return $this->hasMany(CheckpointFact::class)->orderBy('order');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(CheckpointProgress::class);
    }

    // ─── Haversine Distance ───────────────────────────────────────

    /**
     * Geodesic distance from this checkpoint to a GPS point, in metres.
     *
     *   a = sin²(Δφ/2) + cos(φ1)·cos(φ2)·sin²(Δλ/2)
     *   c = 2·atan2(√a, √(1−a))
     *   d = R·c   (R = 6 371 000 m)
     */
    public function calculateDistance(float $lat, float $lon): float
    {
        $R  = 6_371_000;
        $φ1 = deg2rad((float) $this->latitude);
        $φ2 = deg2rad($lat);
        $Δφ = deg2rad($lat  - (float) $this->latitude);
        $Δλ = deg2rad($lon  - (float) $this->longitude);

        $a = sin($Δφ / 2) ** 2 + cos($φ1) * cos($φ2) * sin($Δλ / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }

    // ─── Geofencing ──────────────────────────────────────────────

    /**
     * Returns true when the GPS point is inside this checkpoint's geofence.
     * Uses the stored detection_radius (default 50 m).
     */
    public function isWithinRadius(float $lat, float $lon): bool
    {
        $radius   = $this->detection_radius ?? 50;
        $distance = $this->calculateDistance($lat, $lon);

        return $distance <= $radius;
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
