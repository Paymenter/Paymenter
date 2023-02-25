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
     * Repaginate the data for API.
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
