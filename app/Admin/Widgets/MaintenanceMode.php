<?php

namespace App\Admin\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MaintenanceMode extends Page
{
    protected string $view = 'admin.pages.maintenance-mode';

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $activeNavigationIcon = 'heroicon-s-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Maintenance Mode';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 99;

    public bool $isDown = false;

    public ?string $secret = null;

    public function mount(): void
    {
        $this->isDown = app()->isDownForMaintenance();
        $this->secret = Cache::get('maintenance_secret');
    }

    public function enable(): void
    {
        $secret = bin2hex(random_bytes(16));

        Artisan::call('down', [
            '--secret' => $secret,
            '--retry'  => 60,
        ]);

        Cache::put('maintenance_secret', $secret, now()->addHours(24));

        $this->isDown = true;
        $this->secret = $secret;

        Notification::make()
            ->title('Maintenance mode enabled')
            ->body('The site is now offline for visitors.')
            ->warning()
            ->send();
    }

    public function disable(): void
    {
        Artisan::call('up');

        Cache::forget('maintenance_secret');

        $this->isDown = false;
        $this->secret = null;

        Notification::make()
            ->title('Maintenance mode disabled')
            ->body('The site is back online.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enable')
                ->label('Enable Maintenance')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Enable Maintenance Mode?')
                ->modalDescription('Visitors will see a maintenance page. You will remain unaffected as an admin.')
                ->action(fn () => $this->enable())
                ->visible(fn () => ! $this->isDown),

            Action::make('disable')
                ->label('Disable Maintenance')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Disable Maintenance Mode?')
                ->modalDescription('The site will become accessible to all visitors again.')
                ->action(fn () => $this->disable())
                ->visible(fn () => $this->isDown),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermission('admin.settings.edit');
    }
}