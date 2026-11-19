<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;

class DomainPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.domains.viewAny');
    }

    public function view(User $user, Domain $domain): bool
    {
        return $this->adminPermission($user, 'admin.domains.view', $domain->service?->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('admin.domains.create');
    }

    public function update(User $user, Domain $domain): bool
    {
        return $this->adminPermission($user, 'admin.domains.update', $domain->service?->user_id === $user->id);
    }

    public function delete(User $user, Domain $domain): bool
    {
        return $user->hasPermission('admin.domains.delete');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.domains.deleteAny');
    }
}
