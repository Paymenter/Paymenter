<?php

namespace App\Policies;

use App\Models\AdjustmentNote;
use App\Models\User;

class AdjustmentNotePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.adjustment_notes.viewAny');
    }

    public function view(User $user, AdjustmentNote $adjustmentNote): bool
    {
        return $this->adminPermission($user, 'admin.adjustment_notes.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admin.adjustment_notes.create');
    }

    public function update(User $user, AdjustmentNote $adjustmentNote): bool
    {
        return $this->adminPermission($user, 'admin.adjustment_notes.update');
    }

    public function delete(User $user, AdjustmentNote $adjustmentNote): bool
    {
        return $user->hasPermission('admin.adjustment_notes.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.adjustment_notes.deleteAny');
    }
}
