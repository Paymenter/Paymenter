<?php

namespace App\Policies;

use App\Models\User;

class FailedJobPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.failed_jobs.viewAny');
    }
}
