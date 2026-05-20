<?php

namespace App\Admin\Pages;

use App\Console\Commands\CheckForUpdates;
use App\Console\Commands\Upgrade;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class Updates extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-loop-left-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-loop-left-fill';

    protected string $view = 'admin.pages.updates';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    public static function getNavigationLabel(): string
    {
        return __('updates.updates');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('updates.updates');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkUpdates')
                ->action(function () {
                    Artisan::call(CheckForUpdates::class);
                })
                ->label(__('updates.check_for_updates')),
        ];
    }

    public function update(): Action
    {
        return Action::make('update')
            ->requiresConfirmation()
            ->action(function () {
                $output = new BufferedOutput;

                // Check if current chdir is the root of the project
                if (getcwd() !== base_path()) {
                    chdir(base_path());
                }

                if (config('app.version') == 'beta') {
                    Artisan::call(Upgrade::class, ['--no-interaction' => true, '--url' => 'https://api.paymenter.org/beta'], $output);
                } else {
                    Artisan::call(Upgrade::class, ['--no-interaction' => true], $output);
                }
                $this->dispatch('update-completed', [
                    'output' => $output->fetch(),
                ]);
            })
            ->label(__('updates.update'));
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('admin.updates.update');
    }
}
