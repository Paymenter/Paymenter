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
        return view(static::$view, [
            'sessions' => \App\Models\Session::query()
                ->where('last_activity', '>=', now()->subMinutes(5))
                ->where('user_id', '!=', null)
                ->orderBy('last_activity', 'desc')
                ->with('user')
                ->limit(5)
                ->get(),
        ]);
    }

    public static function canView(): bool
    {
        return auth()->user()->hasPermission('admin.widgets.active_users');
    }
}
