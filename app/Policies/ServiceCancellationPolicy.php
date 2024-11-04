<?php

namespace App\Policies;

use App\Models\ServiceCancellation;
use App\Models\User;

class ServiceCancellationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.service_cancellations.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceCancellation $category): bool
    {
        return $user->hasPermission('admin.service_cancellations.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('admin.service_cancellations.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceCancellation $category): bool
    {
        return $user->hasPermission('admin.service_cancellations.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceCancellation $category): bool
    {
        return $user->hasPermission('admin.service_cancellations.delete');
    }

    /**
     * Determine whether the user can bulk delete the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.service_cancellations.deleteAny');
    }
}
