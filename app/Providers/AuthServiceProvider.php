<?php

namespace App\Providers;

use App\Models\Location;
use App\Models\Geofence;
use App\Policies\LocationPolicy;
use App\Policies\GeofencePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        TourBooking::class => TourBookingPolicy::class,
        Location::class => LocationPolicy::class,
        Geofence::class => GeofencePolicy::class,
    ];

    
    public function boot(): void
    {
    }
}
