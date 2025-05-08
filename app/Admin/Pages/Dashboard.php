<?php

namespace App\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'ri-function-line';

    protected static ?string $activeNavigationIcon = 'ri-function-fill';
}
