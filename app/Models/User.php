<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

   
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'lat',
        'lng',
        'last_location_update',
        'sharing_enabled',
        'map_style',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

   
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_location_update' => 'datetime',
            'sharing_enabled' => 'boolean',
        ];
    }

    
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    
    public function locationHistory()
    {
        return $this->hasMany(LocationHistory::class);
    }

    
    public function geofences()
    {
        return $this->hasMany(Geofence::class);
    }

  
public function tourBookings()
{
    return $this->hasMany(TourBooking::class);
}
}
