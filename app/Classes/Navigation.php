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
                'icon' => 'ri-home-2-fill',
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
                'icon' => 'ri-shopping-bag-fill',
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
                'icon' => 'ri-layout-row-fill',
            ],
            [
                'name' => __('navigation.services'),
                'route' => 'services',
                'icon' => 'ri-server-fill',
            ],
            [
                'name' => __('navigation.invoices'),
                'route' => 'invoices',
                'icon' => 'ri-receipt-fill',
                'separator' => true,
            ],
            [
                'name' => __('navigation.tickets'),
                'route' => 'tickets',
                'icon' => 'ri-customer-service-2-fill',
                'separator' => true,
            ],
            [
                'name' => __('navigation.account'),
                'icon' => 'ri-settings-3-fill',
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
        foreach ($routes as $key => $route) {
            if (isset($route['children'])) {
                foreach ($route['children'] as $child) {
                    if (request()->route()->getName() == $child['route']) {
                        $routes[$key]['active'] = true;
                        break;
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
