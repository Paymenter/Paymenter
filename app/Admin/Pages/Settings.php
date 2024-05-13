<?php

namespace App\Admin\Pages;

use App\Classes\Settings as ClassesSettings;
use App\Models\Setting;
use App\Providers\SettingsProvider;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Classes\FilamentInput;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $title = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'admin.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting_values = [];
        foreach (\App\Classes\Settings::settings() as $group => $settings) {
            foreach ($settings as $setting) {
                $setting_values[$setting['name']] = config("settings.{$setting['name']}", $setting['default'] ?? null);
            }
        }

        $this->form->fill($setting_values);
    }

    public function form(Form $form): Form
    {
        $tabs = [];

        foreach (ClassesSettings::settingsObject() as $key => $categories) {
            $tab = Tabs\Tab::make($key)
                ->label(ucwords(str_replace('-', ' ', $key)))
                ->schema(function () use ($categories, $key) {
                    $inputs = [];
                    foreach ($categories as $setting) {
                        $inputs[] = FilamentInput::convert($setting);
                    }
                    return $inputs;
                });

            $tabs[] = $tab;
        }

        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs($tabs)
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $this->authorize('admin.settings.update');
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Get only the settings that have changed
            $avSetting = \App\Classes\Settings::getSetting($key);
            if ($value !== $avSetting->value) {
                $modelSetting = Setting::where('settingable_type', null)->where('key', $key)->update(['value' => $value]);
                if (!$modelSetting) {
                    Setting::create([
                        'key' => $key,
                        'value' => $value,
                        'settingable_type' => null,
                        'type' => $avSetting->database_type ?? 'string',
                        'encrypted' => $avSetting->encrypted ?? false,
                    ]);
                }
            }
        }

        SettingsProvider::flushCache();

        Notification::make()
            ->title('Saved successfully!')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        return $user->hasPermission('admin.settings.view');
    }
}
