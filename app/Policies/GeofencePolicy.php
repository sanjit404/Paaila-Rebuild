<?php

namespace App\Policies;

use App\Models\Geofence;
use App\Models\User;

class GeofencePolicy
{
    /**
     * Determine if the user can update the geofence.
     */
    public function update(User $user, Geofence $geofence): bool
    {
        return $user->id === $geofence->user_id;
    }

    /**
     * Determine if the user can delete the geofence.
     */
    public function delete(User $user, Geofence $geofence): bool
    {
        return $user->id === $geofence->user_id;
    }
}
