<?php

namespace App\Policies;

use App\Models\User;

class BasePolicy
{
    protected function adminPermission(User $user, $permission): bool
    {
        // Only do this if the request starts with /admin
        return (request()->is('admin/*') || request()->routeIs('paymenter.livewire.update')) && $user->hasPermission($permission);
    }
}
