<?php

namespace App\Policies;

use App\Models\CustomProperty;
use App\Models\User;

class CustomPropertyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.custom_properties.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CustomProperty $customProperty): bool
    {
        return $user->hasPermission('admin.custom_properties.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('admin.custom_properties.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CustomProperty $customProperty): bool
    {
        return $user->hasPermission('admin.custom_properties.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CustomProperty $customProperty): bool
    {
        return $user->hasPermission('admin.custom_properties.delete');
    }

    /**
     * Determine whether the user can bulk delete the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.custom_properties.deleteAny');
    }
}
