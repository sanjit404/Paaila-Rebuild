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
        'image',
        'detection_radius',
        'estimated_time_from_previous'
    ];

    protected $casts = [
        'latitude'         => 'decimal:8',
        'longitude'        => 'decimal:8',
        'order'            => 'integer',
        'detection_radius' => 'integer',
    ];


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

    public function isWithinRadius(float $lat, float $lon): bool
    {
        $radius   = $this->detection_radius ?? 50;
        $distance = $this->calculateDistance($lat, $lon);

        return $distance <= $radius;
    }


    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
