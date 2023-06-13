<?php

namespace App\Classes;

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
        'admin:api:read',
        'admin:api:create',
        'admin:api:delete',
        'admin:api:update',
    ];

    /**
     * Repaginate the data for API.
     * 
     * @return array
     */
    public static function repaginate(mixed $data)
    {
        $data = $data->toArray();

        return [
            'metadata' => [
                'total_items' => $data['total'],
                'total_pages' => $data['last_page'],
                'max_per_page' => $data['per_page'],
                'item_count' => count($data['data']),
            ],
            'data' => $data['data'],
        ];
    }
}
