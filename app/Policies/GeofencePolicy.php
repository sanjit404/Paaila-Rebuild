<?php

namespace App\Policies;

use App\Models\Geofence;
use App\Models\User;

class GeofencePolicy
{
    public function update(User $user, Geofence $geofence): bool
    {
        return $user->id === $geofence->user_id;
    }

    public function delete(User $user, Geofence $geofence): bool
    {
        return $user->id === $geofence->user_id;
    }
}
