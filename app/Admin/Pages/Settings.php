<?php

namespace App\Admin\Pages;

use App\Classes\FilamentInput;
use App\Classes\Settings as ClassesSettings;
use App\Models\Setting;
use App\Providers\SettingsProvider;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

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
                ->schema(function () use ($categories) {
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
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    public function save(bool $silent = false): void
    {
        Gate::authorize('has-permission', 'admin.settings.update');

        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Get only the settings that have changed
            $avSetting = \App\Classes\Settings::getSetting($key);
            if ($value !== $avSetting->value) {
                $setting = Setting::where('settingable_type', null)->where('key', $key)->first();
                if ($setting) {
                    $setting->update([
                        'value' => $value,
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

        SettingsProvider::flushCache();

        if (!$silent) {
            Notification::make()
                ->title('Saved successfully!')
                ->success()
                ->send();
        }
    }

    public function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->submit('save'),
            Actions\Action::make('testEmail')
                ->label('Send Test Email')
                ->color('gray')
                ->icon('heroicon-o-envelope')
                ->action('sendTestEmail')
                ->tooltip('Send a test email to verify your email settings'),
        ];
    }

    public function sendTestEmail(): void
    {
        Gate::authorize('has-permission', 'admin.settings.update');

        $user = auth()->user();
        
        try {
            // Save the form data without showing the success notification
            $this->save(silent: true);
            
            // Send the test email
            Mail::to($user->email)->send(new TestMail());
            
            Notification::make()
                ->title('Test email sent successfully to ' . $user->email)
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to send test email. Read log for more information')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
                
            // Log the error for debugging
            \Log::error('Failed to send test email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return $user && $user->hasPermission('admin.settings.view');
    }
}
