<?php

namespace App\Policies;

use App\Models\OauthClient;
use App\Models\User;

class OauthClientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.oauth_clients.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OauthClient $oauthClient): bool
    {
        return $user->hasPermission('admin.oauth_clients.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('admin.oauth_clients.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OauthClient $oauthClient): bool
    {
        return $user->hasPermission('admin.oauth_clients.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OauthClient $oauthClient): bool
    {
        return $user->hasPermission('admin.oauth_clients.delete');
    }

    /**
     * Determine whether the user can bulk delete the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.oauth_clients.deleteAny');
    }
}
