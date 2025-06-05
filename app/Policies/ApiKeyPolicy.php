<?php

namespace App\Policies;

use App\Models\ApiKey;
use App\Models\User;

class ApiKeyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.api_keys.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ApiKey $api_key): bool
    {
        return $user->hasPermission('admin.api_keys.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('admin.api_keys.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ApiKey $api_key): bool
    {
        return $user->hasPermission('admin.api_keys.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ApiKey $api_key): bool
    {
        return $user->hasPermission('admin.api_keys.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.api_keys.deleteAny');
    }
}
