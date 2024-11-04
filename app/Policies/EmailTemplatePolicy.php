<?php

namespace App\Policies;

use App\Models\EmailTemplate;
use App\Models\User;

class EmailTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.email_templates.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmailTemplate $emailTemplate): bool
    {
        return $user->hasPermission('admin.email_templates.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('admin.email_templates.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmailTemplate $emailTemplate): bool
    {
        return $user->hasPermission('admin.email_templates.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmailTemplate $emailTemplate): bool
    {
        return $user->hasPermission('admin.email_templates.delete');
    }

    /**
     * Determine whether the user can bulk delete the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.email_templates.deleteAny');
    }
}
