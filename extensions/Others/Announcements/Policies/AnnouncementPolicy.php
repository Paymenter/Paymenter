<?php

namespace Paymenter\Extensions\Others\Announcements\Policies;

use App\Models\User;
use App\Policies\BasePolicy;
use Paymenter\Extensions\Others\Announcements\Models\Announcement;

class AnnouncementPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.announcements.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Announcement $announcement): bool
    {
        return $user->hasPermission('admin.announcements.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('admin.announcements.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Announcement $announcement): bool
    {
        return $user->hasPermission('admin.announcements.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->hasPermission('admin.announcements.delete');
    }

    /**
     * Determine whether the user can delete multiple models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.announcements.delete');
    }
}
