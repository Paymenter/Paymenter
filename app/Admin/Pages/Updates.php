<?php

namespace App\Admin\Pages;

use App\Console\Commands\CheckForUpdates;
use App\Console\Commands\Upgrade;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Output\BufferedOutput;

class Updates extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'admin.pages.updates';

    protected static ?string $navigationGroup = 'System';

    public $output = 'trestdasfdsa';

    protected function getHeaderActions(): array
    {

        return [
            Action::make('checkUpdates')
                ->action(function () {
                    Artisan::call(CheckForUpdates::class);
                })
                ->label('Check for updates')
        ];
    }

    public function update(): Action
    {
        return Action::make('update')
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
                // Execute the update command
                $outputContent = $output->fetch();
                $this->output = $outputContent;
            })
            ->label('Update');
    }
}
