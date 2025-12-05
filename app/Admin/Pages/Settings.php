<?php

namespace App\Admin\Pages;

use App\Classes\FilamentInput;
use App\Classes\Settings as ClassesSettings;
use App\Models\EmailLog;
use App\Models\Setting;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Illuminate\Support\Facades\Log;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?string $title = 'Settings';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-settings-3-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-settings-3-fill';

    protected string $view = 'admin.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting_values = [];
        foreach (ClassesSettings::settings() as $group => $settings) {
            foreach ($settings as $setting) {
                $setting_values[$setting['name']] = config("settings.{$setting['name']}", $setting['default'] ?? null);
            }
        }

        $this->form->fill($setting_values);
    }

    public function form(Schema $schema): Schema
    {
        $tabs = [];

        foreach (ClassesSettings::settings() as $key => $categories) {
            $tab = Tab::make($key)
                ->label(ucwords(str_replace('-', ' ', $key)))
                ->schema(function () use ($categories, $key) {
                    $inputs = [];
                    foreach ($categories as $setting) {
                        $inputs[] = FilamentInput::convert($setting);
                    }
                    if ($key === 'theme') {
                        // Add a reset colors button if there are color settings
                        array_unshift($inputs, Actions::make([
                            Action::make('resetColors')
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
                    
                    if ($key === 'mail') {
                        // Add a test email button
                        array_unshift($inputs, Actions::make([
                            Action::make('testEmail')
                                ->label('Send Test Email')
                                ->color('primary')
                                ->icon('heroicon-o-envelope')
                                ->action(fn () => $this->testEmail()),
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

        return $schema
            ->components([
                Form::make([
                    Tabs::make('Tabs')
                        ->tabs($tabs)
                        ->persistTabInQueryString(),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
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
            $avSetting = (object) collect(ClassesSettings::settings())->flatten(1)->firstWhere('name', $key);
            $avSetting->value = $settings[$key]->value ?? $avSetting->default ?? null;

            if ($value !== $avSetting->value || (($avSetting->database_type ?? 'string') === 'boolean' && (bool) $value !== (bool) $avSetting->value)) {
                if ($setting = $settings[$key] ?? null) {
                    $setting->update([
                        'value' => $value,
                        'type' => $avSetting->database_type ?? 'string',
                        'encrypted' => $avSetting->encrypted ?? false,
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
        foreach (ClassesSettings::settings() as $group => $settings) {
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

    public function testEmail(): void
    {
        Gate::authorize('has-permission', 'admin.settings.update');

        /** @var User */
        $user = auth()->user();
        
        if (!$user || !$user->email) {
            Notification::make()
                ->title('Unable to send test email')
                ->body('No email address found for current user.')
                ->danger()
                ->send();
            return;
        }

        if (config('settings.mail_disable')) {
            Notification::make()
                ->title('Mail is disabled')
                ->body('Please enable mail in settings before sending a test email.')
                ->warning()
                ->send();
            return;
        }

        // Validate mail configuration
        $mailHost = config('settings.mail_host');
        $mailPort = config('settings.mail_port');
        $mailUsername = config('settings.mail_username');
        $mailFromAddress = config('settings.mail_from_address');

        $validationErrors = [];
        if (empty($mailHost)) {
            $validationErrors[] = 'Mail Host is required';
        }
        if (empty($mailPort)) {
            $validationErrors[] = 'Mail Port is required';
        }
        if (empty($mailFromAddress)) {
            $validationErrors[] = 'Mail From Address is required';
        }

        if (!empty($validationErrors)) {
            Notification::make()
                ->title('Mail configuration incomplete')
                ->body('Please configure the following settings: ' . implode(', ', $validationErrors))
                ->danger()
                ->send();
            return;
        }

        $emailLog = null;
        
        try {
            // Create mail
            $mail = new \App\Mail\SystemMail([
                'subject' => 'Test Email from ' . config('app.name'),
                'body' => '<p>This is a test email to verify your mail configuration is working correctly.</p><p>If you received this email, your mail settings are configured properly!</p>',
            ]);

            // Create email log
            $emailLog = EmailLog::create([
                'user_id' => $user->id,
                'subject' => $mail->envelope()->subject,
                'to' => $user->email,
                'body' => $mail->render(),
            ]);

            // Add the email log id to the payload
            $mail->email_log_id = $emailLog->id;

            // Send synchronously for immediate feedback (test emails need instant response)
            // Note: Normal emails use ->queue(), but test emails need ->send() for immediate error feedback
            FacadesMail::to($user->email)->send($mail);

            // Update email log status for synchronous sends
            // (Queue listeners handle this for queued emails, but we need to do it manually for sync)
            $emailLog->update([
                'sent_at' => now(),
                'status' => 'sent',
            ]);

            Notification::make()
                ->title('Test email sent successfully!')
                ->body('A test email has been sent to ' . $user->email . '. Please check your inbox.')
                ->success()
                ->send();
        } catch (TransportExceptionInterface $e) {
            // SMTP/Transport specific errors
            $errorMessage = $this->formatMailError($e);
            
            // Update email log with failure status
            if ($emailLog) {
                $emailLog->update([
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ]);
            }
            
            Log::error('Test email failed (Transport): ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $e,
            ]);

            Notification::make()
                ->title('Failed to send test email')
                ->body($errorMessage)
                ->danger()
                ->persistent()
                ->send();
        } catch (\Exception $e) {
            // General errors
            $errorMessage = 'An unexpected error occurred: ' . $e->getMessage();
            
            // Update email log with failure status
            if ($emailLog) {
                $emailLog->update([
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ]);
            }
            
            Log::error('Test email failed (General): ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $e,
            ]);

            Notification::make()
                ->title('Failed to send test email')
                ->body($errorMessage)
                ->danger()
                ->persistent()
                ->send();
        }
    }

    private function formatMailError(\Throwable $e): string
    {
        $message = $e->getMessage();
        
        // Common error patterns and user-friendly messages
        $patterns = [
            '/Connection timed out/i' => 'Connection to mail server timed out. Check your Mail Host and Port settings.',
            '/Connection refused/i' => 'Connection to mail server was refused. Verify your Mail Host and Port are correct.',
            '/Authentication failed/i' => 'Authentication failed. Please check your Mail Username and Password.',
            '/Invalid address/i' => 'Invalid email address format. Please check your Mail From Address.',
            '/Could not authenticate/i' => 'Could not authenticate with mail server. Verify your credentials.',
            '/Could not connect to host/i' => 'Could not connect to mail server. Check your Mail Host and network connection.',
            '/SSL.*handshake/i' => 'SSL/TLS handshake failed. Try changing Mail Encryption setting (TLS/SSL/None).',
        ];

        foreach ($patterns as $pattern => $friendlyMessage) {
            if (preg_match($pattern, $message)) {
                return $friendlyMessage . "\n\nTechnical details: " . $message;
            }
        }

        // Default message with technical details
        return 'Mail sending failed. Please check your mail configuration settings.' . "\n\nError: " . $message;
    }

    public static function canAccess(): bool
    {
        /** @var User */
        $user = auth()->user();

        return $user && $user->hasPermission('admin.settings.view');
    }
}
