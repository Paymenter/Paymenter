<?php

namespace App\Admin\Widgets;

use Filament\Widgets\Widget;

class ActiveUsers extends Widget
{
    protected static string $view = 'admin.widgets.active-users';

    protected static ?int $sort = 2;

    protected static string $title = 'Active Users';

    public function render(): \Illuminate\View\View
    {
        $baseQuery = \App\Models\Session::query()
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->whereNotNull('user_id')
            ->orderBy('last_activity', 'desc')
            ->with('user');

        $sessions = (clone $baseQuery)->limit(5)->get();
        $onlineCount = (clone $baseQuery)->count();

        return view(static::$view, [
            'sessions' => $sessions,
            'onlineCount' => $onlineCount,
        ]);
    }

    public static function canView(): bool
    {
        return auth()->user()->hasPermission('admin.widgets.active_users');
    }
}
