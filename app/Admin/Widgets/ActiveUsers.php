<?php

namespace App\Admin\Widgets;

use App\Models\Session;
use Filament\Widgets\Widget;
use Illuminate\View\View;

class ActiveUsers extends Widget
{
    protected string $view = 'admin.widgets.active-users';

    protected static ?int $sort = 2;

    protected static string $title = 'Active Users';

    public function render(): View
    {
        $baseQuery = Session::query()
            ->where('last_activity', '>=', now()->subMinutes(5))
            ->whereNotNull('user_id')
            ->orderBy('last_activity', 'desc')
            ->with('user');

        $sessions = (clone $baseQuery)->limit(5)->get();
        $onlineCount = (clone $baseQuery)->count();

        return view($this->view, [
            'sessions' => $sessions,
            'onlineCount' => $onlineCount,
        ]);
    }

    public static function canView(): bool
    {
        return auth()->user()->hasPermission('admin.widgets.active_users');
    }
}
