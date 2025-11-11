<?php

namespace App\Policies;

use App\Models\Extension;
use App\Models\User;

class ExtensionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.extensions.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->hasPermission('admin.extensions.view');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Extension $extension): bool
    {
        return $user->hasPermission('admin.extensions.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function install(User $user): bool
    {
        return $user->hasPermission('admin.extensions.install');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Extension $extension): bool
    {
        return $user->hasPermission('admin.extensions.delete');
    }
}
