<?php

namespace App\Policies;

use App\Models\Tld;
use App\Models\User;

class TldPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.tlds.viewAny');
    }

    public function view(User $user, Tld $tld): bool
    {
        return $user->hasPermission('admin.tlds.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admin.tlds.create');
    }

    public function update(User $user, Tld $tld): bool
    {
        return $user->hasPermission('admin.tlds.update');
    }

    public function delete(User $user, Tld $tld): bool
    {
        return $user->hasPermission('admin.tlds.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.tlds.deleteAny');
    }
}
