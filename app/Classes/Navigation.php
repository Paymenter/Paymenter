<?php

namespace App\Classes;

use App\Helpers\EventHelper;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class Navigation
{
    private const GROUPS = [
        'primary' => [
            'order' => 1,
            'separator' => true,
        ],
        'dashboard' => [
            'order' => 2,
            'separator' => true,
        ],
        'support' => [
            'order' => 3,
            'separator' => true,
        ],
        'account' => [
            'order' => 4,
            'separator' => false,
        ],
    ];

    public static function get()
    {
        $categories = Category::whereNull('parent_id')
            ->where(function ($query) {
                $query->whereHas('children')->orWhereHas('products');
            })->get();

        $routes = [
            'primary' => [
                ...self::GROUPS['primary'],
                'items' => [
                    [
                        'name' => __('navigation.home'),
                        'route' => 'home',
                        'icon' => 'ri-home-2-fill',
                        'children' => [],
                    ]
                ]
            ]
        ];

        if (count($categories) > 0) {
            $routes['primary']['items'][] = [
                'name' => __('navigation.shop'),
                'icon' => 'ri-shopping-bag-fill',
                'children' => $categories->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'route' => 'category.show',
                        'icon' => 'ri-shopping-bag-fill',
                        'params' => ['category' => $category->slug],
                    ];
                })->toArray(),
            ];
        }

        if (Auth::user()) {

            $routes['dashboard'] = [
                ...self::GROUPS['dashboard'],
                'items' => [
                    [
                        'name' => __('navigation.dashboard'),
                        'route' => 'dashboard',
                        'icon' => 'ri-layout-row-fill',
                        'children' => [],
                    ],
                    [
                        'name' => __('services.services'),
                        'route' => 'services',
                        'icon' => 'ri-server-fill',
                        'children' => [],
                    ],
                ]
            ];

            $routes['support'] = [
                ...self::GROUPS['support'],
                'items' => [
                    [
                        'name' => __('navigation.tickets'),
                        'route' => 'tickets',
                        'icon' => 'ri-customer-service-2-fill',
                        'children' => [],
                    ],
                ]
            ];

            $routes['account'] = [
                ...self::GROUPS['account'],
                'items' => [
                    [
                        'name' => __('navigation.account'),
                        'route' => 'account',
                        'icon' => 'ri-settings-3-fill',
                        'children' => [],
                    ],
                ]
            ];
        }

        $events = EventHelper::itemEvent('navigation', []);
        foreach ($events as $event) {
            if (isset($event['group']) && isset($routes[$event['group']])) {
                $routes[$event['group']]['items'][] = $event;
            }
        }

        return self::markActiveRoutes($routes);
    }

    public static function getAuth()
    {
        $routes = [
            [
                'name' => __('navigation.dashboard'),
                'route' => 'dashboard',
                'icon' => 'ri-layout-row-fill',
                'children' => [],
            ],
            [
                'name' => __('navigation.tickets'),
                'route' => 'tickets',
                'icon' => 'ri-customer-service-2-fill',
                'children' => [],
            ],
            [
                'name' => __('navigation.account'),
                'route' => 'account',
                'icon' => 'ri-settings-3-fill',
                'children' => [],
            ],
        ];

        if (Auth::user()->role_id) {
            $routes[] = [
                'name' => __('navigation.admin'),
                'route' => 'filament.admin.pages.dashboard',
                'icon' => 'ri-admin-fill',
                'spa' => false,
            ];
        }

        return self::markActiveRoutes($routes);
    }

    public static function getCurrent()
    {
        $route = request()->route()->getName();
        $admin = self::get();

        $parent = null;
        foreach ($admin as $group) {
            foreach ($group['items'] as $item) {
                if (isset($item['route']) && $item['route'] == $route) {
                    $parent = $item;
                    break 2;
                }
                if (isset($item['children'])) {
                    foreach ($item['children'] as $child) {
                        if ($child['route'] == $route) {
                            $parent = $item;
                            break 3;
                        }
                    }
                }
            }
        }
        return $parent;
    }

    private static function markActiveRoutes($routes)
    {
        $currentRoute = request()->route()->getName();

        if (isset($routes['items'])) {
            foreach ($routes['items'] as $key => $route) {
                if (isset($route['route']) && $route['route'] === $currentRoute) {
                    $routes['items'][$key]['active'] = true;
                }
                if (isset($route['children']) && !empty($route['children'])) {
                    foreach ($route['children'] as $childKey => $child) {
                        if (isset($child['route']) && $child['route'] === $currentRoute) {
                            $routes['items'][$key]['active'] = true;
                            $routes['items'][$key]['children'][$childKey]['active'] = true;
                        }
                    }
                }
            }
        } else {
            foreach ($routes as $groupKey => $group) {
                if (isset($group['items'])) {
                    foreach ($group['items'] as $itemKey => $item) {
                        if (isset($item['route']) && $item['route'] === $currentRoute) {
                            $routes[$groupKey]['items'][$itemKey]['active'] = true;
                        }
                        if (isset($item['children']) && !empty($item['children'])) {
                            foreach ($item['children'] as $childKey => $child) {
                                if (isset($child['route']) && $child['route'] === $currentRoute) {
                                    $routes[$groupKey]['items'][$itemKey]['active'] = true;
                                    $routes[$groupKey]['items'][$itemKey]['children'][$childKey]['active'] = true;
                                }
                            }
                        }
                    }
                } else {
                    if (isset($routes[$groupKey]['route']) && $routes[$groupKey]['route'] === $currentRoute) {
                        $routes[$groupKey]['active'] = true;
                    }
                    if (isset($routes[$groupKey]['children']) && !empty($routes[$groupKey]['children'])) {
                        foreach ($routes[$groupKey]['children'] as $childKey => $child) {
                            if (isset($child['route']) && $child['route'] === $currentRoute) {
                                $routes[$groupKey]['active'] = true;
                                $routes[$groupKey]['children'][$childKey]['active'] = true;
                            }
                        }
                    }
                }
            }
        }

        return $routes;
    }
}
