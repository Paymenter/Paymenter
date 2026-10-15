<?php

namespace App\Policies;

use App\Models\Audit;
use App\Models\User;

class AuditPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.audits.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Audit $audit): bool
    {
        return $user->hasPermission('admin.audits.viewAny');
    }

    /**
     * Audits are only written by the auditing package. These methods are spelled out
     * because Filament allows an action whose policy method does not exist.
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Audit $audit): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Audit $audit): bool
    {
        return $user->hasPermission('admin.audits.delete');
    }

    /**
     * Determine whether the user can bulk delete the models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.audits.deleteAny');
    }

    public function forceDelete(User $user, Audit $audit): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Audit $audit): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Audit $audit): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
