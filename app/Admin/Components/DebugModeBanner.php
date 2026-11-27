<?php

namespace App\Admin\Components;

use App\Models\Setting;
use App\Providers\SettingsProvider;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class DebugModeBanner extends Component
{
    public bool $isDebugEnabled = false;

    public function mount(): void
    {
        $this->refreshStatus();
    }

    #[On('debug-mode-updated')]
    public function refreshStatus(): void
    {
        $this->isDebugEnabled = (bool) config('settings.debug', false);
    }

    public function disableDebugMode(): void
    {
        Gate::authorize('has-permission', 'admin.settings.update');

        Setting::updateOrCreate(
            [
                'key' => 'debug',
                'settingable_type' => null,
            ],
            [
                'value' => false,
                'type' => 'boolean',
                'encrypted' => false,
            ]
        );

        SettingsProvider::flushCache();

        $this->isDebugEnabled = false;

        Notification::make()
            ->title('Debug Mode Disabled')
            ->body('Debug mode has been successfully disabled.')
            ->success()
            ->send();

        $this->redirect(request()->header('Referer', url('/admin')), navigate: true);
    }

    public function render()
    {
        return view('admin.components.debug-mode-banner', [
            'canUpdateSettings' => Auth::user()?->hasPermission('admin.settings.update') ?? false,
        ]);
    }
}
