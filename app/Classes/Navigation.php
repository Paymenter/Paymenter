<?php

namespace App\Classes;


class Navigation
{
    public static function getAdmin()
    {
        $routes =  [
            [
                'name' => 'Dashboard',
                'route' => 'admin.index',
                'children' => [],
            ],
            [
                'name' => 'Users',
                'route' => 'admin.users.index',
                'children' => []
            ],
            [
                'name' => 'Configuration',
                'route' => 'admin.settings',
                'children' => [
                    [
                        'name' => 'Settings',
                        'route' => 'admin.settings',
                    ],
                    [
                        'name' => 'System Health',
                        'route' => 'admin.health',
                    ],
                ]
            ],
            // [
            //     'name' => 'Users',
            //     'route' => 'admin.users',
            //     'children' => []
            // ],
        ];

        // Check which one is active
        foreach ($routes as $key => $route) {
            if (isset($route['children'])) {
                foreach ($route['children'] as $child) {
                    if (request()->route()->getName() == $child['route']) {
                        $routes[$key]['active'] = true;
                    }
                }
            } else {
                if (request()->route()->getName() == $route['route']) {
                    $routes[$key]['active'] = true;
                }
            }
        }

        return $routes;
    }

    public static function getCurrent()
    {
        $route = request()->route()->getName();
        $admin = self::getAdmin();
        // Get current parnet of the route
        $parent = null;
        foreach ($admin as $item) {
            if ($item['route'] == $route) {
                $parent = $item;
                break;
            }
            if (isset($item['children'])) {
                foreach ($item['children'] as $child) {
                    if ($child['route'] == $route) {
                        $parent = $item;
                        break;
                    }
                }
            }
        }

        return $parent;
    }
}
