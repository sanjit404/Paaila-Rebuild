<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourPackage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'difficulty_level',
        'max_participants',
        'image',
        'start_location_name',
        'start_lat',
        'start_lng',
        'end_location_name',
        'end_lat',
        'end_lng',
        'is_active',
        'tags',
        'season',
        'region',
        'trek_type'
    ];

   protected $casts = [
    'price'     => 'decimal:2',
    'is_active' => 'boolean',
    'tags'      => 'array',
    'season'    => 'array', 
];

    public function checkpoints(): HasMany
    {
        return $this->hasMany(Checkpoint::class)->orderBy('order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TourBooking::class);
    }

    public function getTotalCheckpointsAttribute()
    {
        return $this->checkpoints()->count();
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rs. ' . number_format($this->price, 2);
    }

    public function getRouteCoordinatesAttribute()
    {
        $coordinates = [
            ['lat' => $this->start_lat, 'lng' => $this->start_lng]
        ];

        foreach ($this->checkpoints as $checkpoint) {
            $coordinates[] = [
                'lat' => $checkpoint->latitude,
                'lng' => $checkpoint->longitude
            ];
        }

        $coordinates[] = ['lat' => $this->end_lat, 'lng' => $this->end_lng];

        return $coordinates;
    }
}
