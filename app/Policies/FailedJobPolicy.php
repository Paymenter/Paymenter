<?php

namespace App\Policies;

use App\Models\FailedJob;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FailedJobPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.failed-jobs.viewAny');
    }
}
