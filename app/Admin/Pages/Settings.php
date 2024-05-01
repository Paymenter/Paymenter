<?php

namespace App\Admin\Pages;

use App\Classes\Settings as ClassesSettings;
use App\Models\Setting;
use App\Providers\SettingsProvider;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $title = 'Configuration';

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
                        switch ($setting->type) {
                            case ('select'):
                                $inputs[] = Select::make($setting->name)
                                    ->label($setting->label ?? $setting->name)
                                    ->options(function () use ($setting) {
                                        // Check if options are associative array or sequential array
                                        if (array_is_list((array) $setting->options)) {
                                            // If yes, then return array which has the keys same as the values
                                            $options_with_keys = array_merge(...array_map(fn ($item) => [$item => $item], $setting->options));
                                            return $options_with_keys;
                                        } else {
                                            return (array) $setting->options;
                                        }
                                    })
                                    ->native(true)
                                    ->multiple($setting->multiple ?? false)
                                    ->searchable()
                                    ->rules($setting->validation ?? []);
                                break;

                            case ('text'):
                                $inputs[] = TextInput::make($setting->name)
                                    ->label($setting->label ?? $setting->name)
                                    ->placeholder($setting->default ?? "")
                                    ->required($setting->required ?? false)
                                    ->rules($setting->validation ?? []);
                                break;
                            case ('password'):
                                $inputs[] = TextInput::make($setting->name)
                                    ->label($setting->label ?? $setting->name)
                                    ->placeholder($setting->default ?? "")
                                    ->required($setting->required ?? false)
                                    ->password()
                                    ->revealable()
                                    ->rules($setting->validation ?? []);
                                break;
                            case ('email'):
                                $inputs[] = TextInput::make($setting->name)
                                    ->label($setting->label ?? $setting->name)
                                    ->placeholder($setting->default ?? "")
                                    ->required($setting->required ?? false)
                                    ->email()
                                    ->rules($setting->validation ?? []);
                                break;
                            case ('number'):
                                $inputs[] = TextInput::make($setting->name)
                                    ->label($setting->label ?? $setting->name)
                                    ->placeholder($setting->default ?? "")
                                    ->required($setting->required ?? false)
                                    ->numeric()
                                    ->rules($setting->validation ?? []);

                                break;
                            case ('color'):
                                $inputs[] = ColorPicker::make($setting->name)
                                    ->label($setting->label ?? $setting->name)
                                    ->placeholder($setting->default ?? "")
                                    ->required($setting->required ?? false)
                                    ->hexColor()
                                    ->rules($setting->validation ?? []);
                                break;
                            case ('file'):
                                $input =  FileUpload::make($setting->name)
                                    ->label($setting->label ?? $setting->name)
                                    ->required($setting->required ?? false)
                                    ->acceptedFileTypes($setting->accept)
                                    ->rules($setting->validation ?? []);

                                if (isset($setting->file_name)) {
                                    $input->getUploadedFileNameForStorageUsing(
                                        fn (): string => (string) $setting->file_name,
                                    );
                                }
                                $inputs[] = $input;

                                break;

                            case ('checkbox'):
                                $inputs[] = Checkbox::make($setting->name)
                                    ->label($setting->label ?? $setting->name)
                                    ->required($setting->required ?? false)
                                    ->rules($setting->validation ?? []);
                                break;

                            default;
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
