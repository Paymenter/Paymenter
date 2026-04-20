<?php

namespace App\Admin\Actions;

use App\Classes\Settings as ClassesSettings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;

class ResetColorsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'resetColors';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Reset Colors')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Gate::authorize('has-permission', 'admin.settings.update');

                $colorSettings = [];
                foreach (ClassesSettings::settings() as $settings) {
                    foreach ($settings as $setting) {
                        if (($setting['type'] ?? '') === 'color') {
                            $colorSettings[$setting['name']] = $setting['default'] ?? '';
                        }
                    }
                }

                $livewire = $this->getLivewire();
                $currentData = $livewire->form->getState();
                foreach ($colorSettings as $key => $defaultValue) {
                    $currentData[$key] = $defaultValue;
                }
                $livewire->form->fill($currentData);

                Notification::make()
                    ->title('Colors has been reset!')
                    ->success()
                    ->send();
            });
    }
}
