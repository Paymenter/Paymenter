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

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'admin.pages.updates';

    protected static ?string $navigationGroup = 'System';

    public $output = '';

    protected function getHeaderActions(): array
    {

        return [
            Action::make('checkUpdates')
                ->action(function () {
                    Artisan::call(CheckForUpdates::class);
                })
                ->label('Check for updates'),
        ];
    }

    public function update(): Action
    {
        return Action::make('update')
            ->action(function () {
                $output = new BufferedOutput;

                // Execute the update command
                Artisan::call(Upgrade::class, ['--no-interaction'], $output);
                $outputContent = $output->fetch();
                $this->output = $outputContent;
            })
            ->label('Update');
    }
}
