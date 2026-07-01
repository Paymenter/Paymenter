<?php

namespace App\Policies;

use App\Models\LocationGroup;
use App\Models\User;

class LocationGroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.location_groups.viewAny');
    }

    public function view(User $user, LocationGroup $locationGroup): bool
    {
        return $user->hasPermission('admin.location_groups.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admin.location_groups.create');
    }

    public function update(User $user, LocationGroup $locationGroup): bool
    {
        return $user->hasPermission('admin.location_groups.update');
    }

    public function delete(User $user, LocationGroup $locationGroup): bool
    {
        return $user->hasPermission('admin.location_groups.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.location_groups.deleteAny');
    }
}
