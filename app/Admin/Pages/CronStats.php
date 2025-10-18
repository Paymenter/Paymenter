<?php

namespace App\Admin\Pages;

use App\Admin\Widgets\CronStat\CronOverview;
use App\Admin\Widgets\CronStat\CronStat;
use App\Admin\Widgets\CronStat\CronTable;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Illuminate\Support\Facades\Auth;

class CronStats extends Dashboard
{
    use HasFiltersAction;

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?string $title = 'Cron Statistics';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-time-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-time-fill';

    protected static ?int $navigationSort = 4;

    protected static string $routePath = 'cron-stats';

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->slideOver(false)
                ->schema([
                    DatePicker::make('date')
                        ->default(now())
                        ->label('Select Date')
                        ->required(),
                ]),
        ];
    }

    public function getWidgets(): array
    {
        // but filter out
        return [
            CronOverview::class,
            CronTable::class,
            CronStat::class,
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermission('admin.cron_stats.view');
    }
}
