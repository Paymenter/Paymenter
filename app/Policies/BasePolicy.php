<?php

namespace App\Policies;

use App\Models\User;
use Filament\Facades\Filament;

class BasePolicy
{
    protected function adminPermission(User $user, string $permission, bool $orCondition = false): bool
    {
        $isAdminRequest = Filament::getCurrentPanel()?->getId() === 'admin';

        return $isAdminRequest
            ? $user->hasPermission($permission)
            : $orCondition;
    }
}
