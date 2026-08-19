<?php

namespace App\Support;

use App\Models\Service;
use App\Models\User;

/**
 * Staff read/write checks for admin service surfaces.
 *
 * ServicePolicy grants staff abilities only on admin requests or Livewire
 * updates (route-guarded in BasePolicy). These helpers also accept the raw
 * permissions so the admin-only call sites (Filament pages and components,
 * which are reachable solely through the admin panel) behave identically in
 * contexts without that request state.
 */
class ServiceAdminAuthorization
{
    public static function canView(?User $user, Service $service): bool
    {
        if (!$user) {
            return false;
        }

        return $user->can('view', $service)
            || $user->hasPermission('admin.services.view')
            || $user->hasPermission('admin.services.viewAny')
            || $user->hasPermission('admin.services.update')
            || $service->user_id === $user->id;
    }

    public static function canUpdate(?User $user, Service $service): bool
    {
        if (!$user) {
            return false;
        }

        return $user->can('update', $service)
            || $user->hasPermission('admin.services.update');
    }

    public static function authorizeUpdate(?User $user, Service $service): void
    {
        abort_unless(static::canUpdate($user, $service), 403);
    }
}
