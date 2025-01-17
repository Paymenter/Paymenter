<?php

namespace App\Classes;

use App\Helpers\EventHelper;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class Navigation
{
    public static function getLinks()
    {
        $categories = Category::whereNull('parent_id')->where(function ($query) {
            $query->whereHas('children')->orWhereHas('products');
        })->get();

        $routes = [
            [
                'name' => __('navigation.home'),
                'route' => 'home',
                'children' => [],
            ], [
                'name' => __('navigation.dashboard'),
                'route' => 'dashboard',
                'children' => [],
                'condition' => Auth::user() !== null,
            ], [
                'name' => __('navigation.shop'),
                'children' => $categories->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'route' => 'category.show',
                        'params' => ['category' => $category->slug],
                    ];
                })->toArray(),
                'condition' => count($categories) > 0,
            ],
        ];

        $routes = array_filter($routes, function ($route) {
            return isset($route['condition']) ? $route['condition'] : true;
        });

        $routes = EventHelper::itemEvent('navigation', $routes);

        return Navigation::markActiveRoute($routes);
    }

    // Get navigation items for user dropdown menu
    public static function getAccountDropdownLinks()
    {
        $routes = [
            [
                'name' => __('navigation.dashboard'),
                'route' => 'dashboard',
                'children' => [],
            ],
            [
                'name' => __('navigation.tickets'),
                'route' => 'tickets',
                'children' => [],
            ],
            [
                'name' => __('navigation.account'),
                'route' => 'account',
                'children' => [],
            ],
            [
                'name' => __('navigation.admin'),
                'route' => 'filament.admin.pages.dashboard',
                'spa' => false,
                'condition' => Auth::user()->role_id !== null
            ],
        ];

        $routes = array_filter($routes, function ($route) {
            return isset($route['condition']) ? $route['condition'] : true;
        });

        $routes = EventHelper::itemEvent('navigation.account-dropdown', $routes);

        return Navigation::markActiveRoute($routes);
    }

    // Get navigation items for user account page
    public static function getAccountLinks()
    {
        $routes = [
            [
                'name' => __('account.personal_details'),
                'route' => 'account',
            ],
            [
                'name' => __('account.credits'),
                'route' => 'account.credits',
                'condition' => config('settings.credits_enabled')
            ],
            [
                'name' => __('account.security'),
                'route' => 'account.security',
            ],
        ];

        $routes = array_filter($routes, function ($route) {
            return isset($route['condition']) ? $route['condition'] : true;
        });

        $routes = EventHelper::itemEvent('navigation.account', $routes);

        return Navigation::markActiveRoute($routes);
    }

    /**
     * Set `active` to true if the route is currently active,
     * or falce if route isn't active (prevents `Undefined array key "active"` errors)
     *
     * @return array routes
     */
    public static function markActiveRoute(array $routes): array
    {
        foreach ($routes as $key => $route) {
            if (isset($route['children'])) {
                foreach ($route['children'] as $child) {
                    if (request()->route()->getName() == $child['route']) {
                        $routes[$key]['active'] = true;
                    } else {
                        $routes[$key]['active'] = false;
                    }
                }
            } else {
                if (request()->route()->getName() == $route['route']) {
                    $routes[$key]['active'] = true;
                } else {
                    $routes[$key]['active'] = false;
                }
            }
        }

        return $routes;
    }

    public static function getCurrent()
    {
        $route = request()->route()->getName();
        $routes = self::getLinks();
        // Get current parnet of the route
        $parent = null;
        foreach ($routes as $item) {
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
