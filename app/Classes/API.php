<?php

namespace App\Classes;

use App\Utils\Permissions;

class API
{
    /**
     * All available API permissions.
     * 
     * @return array
     */
    public static $permissions = [
        'invoice:read',
        'invoice:create',
        'invoice:delete',
        'invoice:update',
        'ticket:read',
        'ticket:create',
        'ticket:delete',
        'ticket:update',
        'api:read',
        'api:create',
        'api:delete',
        'api:update',
    ];

    /**
     * All available admin API permissions.
     * 
     * @return array
     */
    public static $adminPermissions = [
        'admin:ticket:read',
        'admin:ticket:create',
        'admin:ticket:delete',
        'admin:ticket:update',
        'admin:invoice:read',
        'admin:invoice:create',
        'admin:invoice:delete',
        'admin:invoice:update',
    ];


    public static $adminPermissionsDifference = [
        'admin:ticket:read' => 'VIEW_TICKETS',
        'admin:ticket:create' => 'CREATE_TICKETS',
        'admin:ticket:delete' => 'DELETE_TICKETS',
        'admin:ticket:update' => 'EDIT_TICKETS',
        'admin:invoice:read' => 'VIEW_INVOICES',
        'admin:invoice:create' => 'CREATE_INVOICES',
        'admin:invoice:delete' => 'DELETE_INVOICES',
        'admin:invoice:update' => 'EDIT_INVOICES',
    ];

    /** 
     * Check if user has permission.
     * 
     * @return bool
     */
    public static function hasPermission($user, $permission)
    {
        $permission = self::$adminPermissionsDifference[$permission];
        $permissions = new Permissions($user->role->permissions);
        return $permissions->has($permission);
    }


    /**
     * Repaginate the data for API.
     * 
     * @return array
     */
    public static function repaginate(mixed $data)
    {
        $data = $data->toArray();

        return [
            'data' => $data['data'],
            'metadata' => [
                'total_items' => $data['total'],
                'total_pages' => $data['last_page'],
                'max_per_page' => $data['per_page'],
                'item_count' => count($data['data']),
            ],
        ];
    }
}
