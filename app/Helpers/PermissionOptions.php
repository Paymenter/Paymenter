<?php

namespace App\Helpers;

class PermissionOptions
{
    /**
     * Add stored permissions that are no longer offered (renamed, or from a disabled or removed extension) to the options.
     * Filament validates the selection against the options, so without this the record could not be saved at all.
     *
     * @param  array<string, string>  $options
     * @return array<string, string>
     */
    public static function withStored(array $options, mixed $stored): array
    {
        foreach (is_array($stored) ? $stored : [] as $permission) {
            if (!is_string($permission) || array_key_exists($permission, $options)) {
                continue;
            }

            $options[$permission] = __('admin.permissions.no_longer_available', ['permission' => $permission]);
        }

        return $options;
    }
}
