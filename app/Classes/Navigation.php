<?php

namespace App\Classes;

use App\Helpers\EventHelper;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class Navigation
{
    public static function getLinks()
    {
        $categories = once(fn () => Category::whereNull('parent_id')->where(function ($query) {
            $query->whereHas('children')->orWhereHas('products');
        })->get());

        $routes = [
            [
                'name' => __('navigation.home'),
                'route' => 'home',
                'icon' => 'ri-home-2',
            ],
            [
                'name' => __('navigation.shop'),
                'children' => $categories->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'route' => 'category.show',
                        'params' => ['category' => $category->slug],
                    ];
                })->toArray(),
                'condition' => count($categories) > 0,
                'separator' => true,
                'icon' => 'ri-shopping-bag',
            ],
        ];

        $routes = EventHelper::itemEvent('navigation', $routes);

        $routes = array_filter($routes, function ($route) {
            return isset($route['condition']) ? $route['condition'] : true;
        });

        return Navigation::markActiveRoute($routes);
    }

    // Get navigation items for user dropdown menu
    public static function getAccountDropdownLinks()
    {
        $routes = [
            [
                'name' => __('navigation.dashboard'),
                'route' => 'dashboard',
            ],
            [
                'name' => __('navigation.tickets'),
                'route' => 'tickets',
            ],
            [
                'name' => __('navigation.account'),
                'route' => 'account',
            ],
            [
                'name' => __('navigation.admin'),
                'route' => 'filament.admin.pages.dashboard',
                'spa' => false,
                'condition' => Auth::user()->role_id !== null,
            ],
        ];

        $routes = EventHelper::itemEvent('navigation.account-dropdown', $routes);

        $routes = array_filter($routes, function ($route) {
            return isset($route['condition']) ? $route['condition'] : true;
        });

        return Navigation::markActiveRoute($routes);
    }

    public static function getDashboardLinks()
    {
        $routes = [
            [
                'name' => __('navigation.dashboard'),
                'route' => 'dashboard',
                'icon' => 'ri-dashboard',
                'condition' => Auth::check(),
            ],
            [
                'name' => __('navigation.services'),
                'route' => 'services',
                'icon' => 'ri-archive-stack',
                'condition' => Auth::check(),
            ],
            [
                'name' => __('navigation.invoices'),
                'route' => 'invoices',
                'icon' => 'ri-receipt',
                'separator' => true,
                'condition' => Auth::check(),
            ],
            [
                'name' => __('navigation.tickets'),
                'route' => 'tickets',
                'icon' => 'ri-customer-service',
                'separator' => true,
                'condition' => Auth::check(),
            ],
            [
                'name' => __('navigation.account'),
                'icon' => 'ri-settings-3',
                'condition' => Auth::check(),
                'children' => [
                    [
                        'name' => __('navigation.personal_details'),
                        'route' => 'account',
                        'params' => [],
                    ],
                    [
                        'name' => __('navigation.security'),
                        'route' => 'account.security',
                        'params' => [],
                    ],
                    [
                        'name' => __('account.credits'),
                        'route' => 'account.credits',
                        'params' => [],
                        'condition' => config('settings.credits_enabled'),
                    ],
                    ...EventHelper::itemEvent('navigation.account', []),
                ],
            ],
        ];

        $routes = EventHelper::itemEvent('navigation.dashboard', $routes);

        $routes = array_filter($routes, function ($route) {
            return isset($route['condition']) ? $route['condition'] : true;
        });

        return Navigation::markActiveRoute($routes);
    }

    public static function getActiveRoute()
    {
        $route = request()->route()->getName();
        $routes = [
            ...self::getLinks(),
            ...self::getAccountDropdownLinks(),
            ...self::getDashboardLinks(),
        ];
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

    /**
     * Set `active` to true if the route is currently active,
     * or falce if route isn't active (prevents `Undefined array key "active"` errors)
     *
     * @return array routes
     */
    public static function markActiveRoute(array $routes): array
    {
        $currentRoute = request()->route()->getName();
    
        foreach ($routes as &$route) {
            $route['active'] = self::isActiveRoute($route, $currentRoute);
    
            if (isset($route['icon'])) {
                $route['icon'] .= $route['active'] ? '-fill' : '-line';
            }
    
            if (isset($route['children'])) {
                foreach ($route['children'] as &$child) {
                    $child['active'] = self::isActiveRoute($child, $currentRoute);
                }
            }
        }
    
        return $routes;
    }

    private static function isActiveRoute(array $route, string $currentRoute): bool
    {
        if (($route['route'] ?? '') === $currentRoute) {
            return true;
        }

        if (!empty($route['children'])) {
            foreach ($route['children'] as $child) {
                if (($child['route'] ?? '') === $currentRoute) {
                    return true;
                }
            }
        }

        return false;
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
