<?php

namespace App\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'ri-dashboard-line';

    protected static ?string $activeNavigationIcon = 'ri-dashboard-fill';
}
