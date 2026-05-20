<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\TourBooking;
use App\Policies\TourBookingPolicy;

class AppServiceProvider extends ServiceProvider
{
    
   

    
    public function boot(): void
    {
        Gate::policy(\App\Models\Location::class, \App\Policies\LocationPolicy::class);
        Gate::policy(\App\Models\Geofence::class, \App\Policies\GeofencePolicy::class);
        Gate::policy(TourBooking::class, TourBookingPolicy::class);
    }
}
