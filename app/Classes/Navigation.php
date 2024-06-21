<?php

namespace App\Classes;

use App\Models\Category;

class Navigation
{
    public static function get()
    {
        $categories = Category::whereNull('parent_id')->get();

        $routes = [
            [
                'name' => 'Dashboard',
                'route' => 'home',
                'children' => [],
            ],
            [
                'name' => 'Shop',
                'route' => 'home',
                'children' => $categories->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'route' => 'category.show',
                        'params' => ['category' => $category->slug],
                    ];
                })->toArray(),
            ],
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
        $admin = self::get();
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
