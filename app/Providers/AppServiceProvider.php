<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\TourBooking;
use App\Policies\TourBookingPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Gate::policy(\App\Models\Location::class, \App\Policies\LocationPolicy::class);
        Gate::policy(\App\Models\Geofence::class, \App\Policies\GeofencePolicy::class);
        Gate::policy(TourBooking::class, TourBookingPolicy::class);

        // Share active booking with layouts.app
        View::composer('layouts.app', function ($view) {
            $activeBooking = null;

            if (Auth::check()) {
                $activeBooking = TourBooking::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->first();
            }

            $view->with('activeBooking', $activeBooking);
        });
    }
}