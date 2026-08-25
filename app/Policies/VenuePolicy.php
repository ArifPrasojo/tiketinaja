<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy
{
    public function update(User $user, Venue $venue): bool
    {
        return $user->role === 'admin' || $venue->manager_id === $user->id;
    }

    public function delete(User $user, Venue $venue): bool
    {
        return $user->role === 'admin' || $venue->manager_id === $user->id;
    }
}
