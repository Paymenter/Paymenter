<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.services.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Service $service): bool
    {
        return $this->adminPermission($user, 'admin.services.view') || $service->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('admin.services.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Service $service): bool
    {
        return $this->adminPermission($user, 'admin.services.update') || $service->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Service $service): bool
    {
        return $user->hasPermission('admin.services.delete');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.services.deleteAny');
    }
}
