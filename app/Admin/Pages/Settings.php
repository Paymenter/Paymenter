<?php

namespace App\Admin\Pages;

use App\Classes\FilamentInput;
use App\Classes\Settings as ClassesSettings;
use App\Models\Setting;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'System';

    protected static ?string $title = 'Settings';

    protected static ?string $navigationIcon = 'ri-settings-3-line';

    protected static ?string $activeNavigationIcon = 'ri-settings-3-fill';

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

        foreach (ClassesSettings::settings() as $key => $categories) {
            $tab = Tabs\Tab::make($key)
                ->label(ucwords(str_replace('-', ' ', $key)))
                ->schema(function () use ($categories, $key) {
                    $inputs = [];
                    foreach ($categories as $setting) {
                        $inputs[] = FilamentInput::convert($setting);
                    }
                    if ($key === 'theme') {
                        // Add a reset colors button if there are color settings
                        array_unshift($inputs, Actions::make([
                            Actions\Action::make('resetColors')
                                ->label('Reset Colors')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->action(fn () => $this->resetColors()),
                        ]));
                        // Wrap the first two inputs in a group if there are more than one
                        if (count($inputs) > 1) {
                            $inputs[0] = Group::make([
                                $inputs[1]->columnSpan(3),
                                $inputs[0],
                            ])->columns(4)->columnSpanFull();
                            unset($inputs[1]);
                        }
                    }

                    return $inputs;
                });

            $tabs[] = $tab;
        }

        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs($tabs)
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        Gate::authorize('has-permission', 'admin.settings.update');

        $data = $this->form->getState();

        $settings = Setting::where('settingable_type', null)
            ->whereIn('key', array_keys($data))
            ->get()
            ->keyBy('key');

        foreach ($data as $key => $value) {
            // Get only the settings that have changed
            $avSetting = (object) collect(\App\Classes\Settings::settings())->flatten(1)->firstWhere('name', $key);
            $avSetting->value = $settings[$key]->value ?? $avSetting->default ?? null;

            if ($value !== $avSetting->value || (($avSetting->database_type ?? 'string') === 'boolean' && (bool) $value !== (bool) $avSetting->value)) {
                if ($setting = $settings[$key] ?? null) {
                    $setting->update([
                        'value' => $value,
                        'type' => $avSetting->database_type ?? 'string',
                    ]);
                } else {
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

        Notification::make()
            ->title('Saved successfully!')
            ->success()
            ->send();
    }

    public function resetColors(): void
    {
        Gate::authorize('has-permission', 'admin.settings.update');

        $colorSettings = [];
        foreach (\App\Classes\Settings::settings() as $group => $settings) {
            foreach ($settings as $setting) {
                if (($setting['type'] ?? '') === 'color') {
                    $colorSettings[$setting['name']] = $setting['default'] ?? '';
                }
            }
        }

        $currentData = $this->form->getState();
        foreach ($colorSettings as $key => $defaultValue) {
            $currentData[$key] = $defaultValue;
        }
        $this->form->fill($currentData);

        Notification::make()
            ->title('Colors has been reset!')
            ->success()
            ->send();
    }

    public static function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return $user && $user->hasPermission('admin.settings.view');
    }
}
