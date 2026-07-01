<?php

namespace App\Policies;

use App\Models\LocationOption;
use App\Models\User;

class LocationOptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.location_options.viewAny');
    }

    public function view(User $user, LocationOption $locationOption): bool
    {
        return $user->hasPermission('admin.location_options.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admin.location_options.create');
    }

    public function update(User $user, LocationOption $locationOption): bool
    {
        return $user->hasPermission('admin.location_options.update');
    }

    public function delete(User $user, LocationOption $locationOption): bool
    {
        return $user->hasPermission('admin.location_options.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.location_options.deleteAny');
    }
}
