<?php

namespace App\Classes;

use App\Models\Currency;
use App\Models\Setting;
use App\Models\TaxRate;
use App\Models\User;
use App\Rules\Cidr;
use DateTimeZone;
use Exception;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Minishlink\WebPush\VAPID;
use Ramsey\Uuid\Uuid;

class Settings
{
    public static function settings()
    {
        try {
            // Only code is needed
            $currencies = once(function () {
                return Currency::pluck('code')->toArray();
            });
        } catch (Exception $e) {
            $currencies = [];
        }
        $settings = [
            // Split settings into groups (only used in the settings page for organization)
            'general' => [
                [
                    'name' => 'company_name',
                    'label' => 'Company Name',
                    'type' => 'text',
                    'override' => 'app.name',
                    'default' => 'Paymenter',
                ],
                [
                    'name' => 'timezone',
                    'label' => 'Timezone',
                    'type' => 'select',
                    // Read timezones from PHP
                    'options' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                    'default' => 'UTC',
                    'required' => true,
                    'override' => 'app.timezone',
                ],
                [
                    'name' => 'app_language',
                    'label' => 'Default Language',
                    'default' => 'en',
                    'type' => 'select',
                    // Read languages from resources/lang directory
                    // The ternary operator is only present for now. Since there are no lang files, it returns [], which breaks the frontend, so we return ['en']
                    'options' => glob(base_path('lang/*'), GLOB_ONLYDIR) ? array_map('basename', glob(base_path('lang/*'), GLOB_ONLYDIR)) : ['en'],
                    'required' => true,
                    'validation' => 'in:' . implode(',', glob(base_path('lang/*'), GLOB_ONLYDIR) ? array_map('basename', glob(base_path('lang/*'), GLOB_ONLYDIR)) : ['en']),
                    'override' => 'app.locale',
                ],
                [
                    'name' => 'app_url',
                    'label' => 'App URL',
                    'default' => 'http://localhost',
                    'type' => 'text',
                    'required' => true,
                    'validation' => 'url',
                    'override' => 'app.url',
                ],
                [
                    'name' => 'logo',
                    'label' => 'Logo (Light Mode)',
                    'type' => 'file',
                    'required' => false,
                    'accept' => ['image/*'],
                    'file_name' => 'logo-light.webp',
                    'description' => 'Upload a logo to be displayed on light backgrounds.',
                ],
                [
                    'name' => 'logo_dark',
                    'label' => 'Logo (Dark Mode)',
                    'type' => 'file',
                    'required' => false,
                    'accept' => ['image/*'],
                    'file_name' => 'logo-dark.webp',
                    'description' => 'Upload a logo to be displayed on dark backgrounds.',
                ],
                [
                    'name' => 'favicon',
                    'label' => 'Favicon',
                    'type' => 'file',
                    'required' => false,
                    'accept' => ['image/x-icon', 'image/png', 'image/svg+xml'],
                    'file_name' => 'favicon.ico',
                    'description' => 'Upload a .ico, .png, or .svg file to be used as the browser icon.',
                ],
                [
                    'name' => 'system_email_address',
                    'label' => 'System Email Address',
                    'type' => 'email',
                    'required' => true,
                    'description' => 'The email address used for system emails, such as CronJob failures, updates, etc.',
                ],
                [
                    'name' => 'tos',
                    'label' => 'Terms of Service',
                    'description' => 'URL to your terms of service. Leave blank to disable.',
                    'type' => 'text',
                    'required' => false,
                ],
            ],

            // Security (captcha, rate limiting, etc.)
            'security' => [
                [
                    'name' => 'captcha',
                    'label' => 'Captcha',
                    'type' => 'select',
                    'options' => [
                        'disabled' => 'Disabled',
                        'recaptcha-v2' => 'Google reCAPTCHA v2',
                        'recaptcha-v3' => 'Google reCAPTCHA v3',
                        'turnstile' => 'Cloudflare Turnstile',
                        'hcaptcha' => 'hCaptcha',
                    ],
                    'default' => 'disabled',
                ],
                [
                    'name' => 'captcha_site_key',
                    'label' => 'Captcha Site Key',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'name' => 'captcha_secret',
                    'label' => 'Captcha Secret',
                    'type' => 'text',
                    'required' => false,
                ],

                [
                    'name' => 'trusted_proxies',
                    'label' => 'Trusted Proxies',
                    'type' => 'tags',
                    'database_type' => 'array',
                    'placeholder' => 'IP Addresses or CIDR (e.g. 1.1.1.1/32 or 2606:4700:4700::1111)',
                    'nested_validation' => [
                        new Cidr(allowWildCard: true),
                    ],
                ],
            ],

            'social-login' => [
                [
                    'name' => 'oauth_google',
                    'label' => 'Google Enabled',
                    'description' => new HtmlString('<a href="https://paymenter.org/docs/guides/OAuth#google" target="_blank">Documentation</a>'),
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                    'required' => false,
                ],
                [
                    'name' => 'oauth_google_client_id',
                    'label' => 'Google Client ID',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'name' => 'oauth_google_client_secret',
                    'label' => 'Google Client Secret',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'name' => 'oauth_github',
                    'label' => 'GitHub Enabled',
                    'description' => new HtmlString('<a href="https://paymenter.org/docs/guides/OAuth#github" target="_blank">Documentation</a>'),
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                    'required' => false,
                ],
                [
                    'name' => 'oauth_github_client_id',
                    'label' => 'Github Client ID',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'name' => 'oauth_github_client_secret',
                    'label' => 'Github Client Secret',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'name' => 'oauth_discord',
                    'label' => 'Discord Enabled',
                    'description' => new HtmlString('<a href="https://paymenter.org/docs/guides/OAuth#discord" target="_blank">Documentation</a>'),
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                    'required' => false,
                ],
                [
                    'name' => 'oauth_discord_client_id',
                    'label' => 'Discord Client ID',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'name' => 'oauth_discord_client_secret',
                    'label' => 'Discord Client Secret',
                    'type' => 'text',
                    'required' => false,
                ],
            ],
            'tax' => [
                [
                    'name' => 'tax_enabled',
                    'label' => 'Tax Enabled',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                ],
                [
                    'name' => 'tax_type',
                    'label' => 'Tax Type',
                    'type' => 'select',
                    'options' => [
                        'inclusive' => 'Inclusive (Price includes tax)',
                        'exclusive' => 'Exclusive (Price does not include tax)',
                    ],
                    'default' => 'inclusive',
                ],
            ],
            'mail' => [
                // SMTP etc
                [
                    'name' => 'mail_disable',
                    'label' => 'Disable Mail',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'live' => true,
                    'default' => true,
                ],
                [
                    'name' => 'mail_must_verify',
                    'label' => 'Users must verify email before buying',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                ],
                [
                    'name' => 'mail_host',
                    'label' => 'Mail Host',
                    'type' => 'text',
                    'required' => fn (Get $get) => !$get('mail_disable'),
                    'override' => 'mail.mailers.smtp.host',
                ],
                [
                    'name' => 'mail_port',
                    'label' => 'Mail Port',
                    'type' => 'text',
                    'required' => fn (Get $get) => !$get('mail_disable'),
                    'override' => 'mail.mailers.smtp.port',
                ],
                [
                    'name' => 'mail_username',
                    'label' => 'Mail Username',
                    'type' => 'text',
                    'required' => fn (Get $get) => !$get('mail_disable'),
                    'override' => 'mail.mailers.smtp.username',
                ],
                [
                    'name' => 'mail_password',
                    'label' => 'Mail Password',
                    'type' => 'password',
                    'required' => fn (Get $get) => !$get('mail_disable'),
                    'encrypted' => true,
                    'override' => 'mail.mailers.smtp.password',
                ],
                [
                    'name' => 'mail_encryption',
                    'label' => 'Mail Encryption',
                    'type' => 'select',
                    'options' => [
                        'tls' => 'TLS',
                        'ssl' => 'SSL',
                        null => 'None',
                    ],
                    'default' => 'tls',
                    'required' => false,
                    'override' => 'mail.mailers.smtp.encryption',
                ],
                [
                    'name' => 'mail_from_address',
                    'label' => 'Mail From Address',
                    'type' => 'email',
                    'required' => fn (Get $get) => !$get('mail_disable'),
                    'override' => 'mail.from.address',
                ],
                [
                    'name' => 'mail_from_name',
                    'label' => 'Mail From Name',
                    'type' => 'text',
                    'required' => fn (Get $get) => !$get('mail_disable'),
                    'override' => 'mail.from.name',
                ],

                // Theming
                [
                    'name' => 'mail_header',
                    'label' => 'Header',
                    'type' => 'markdown',
                    'required' => fn (Get $get) => !$get('mail_disable'),
                    'default' => '',
                    'disable_toolbar' => true,
                ],
                [
                    'name' => 'mail_footer',
                    'label' => 'Footer',
                    'type' => 'markdown',
                    'required' => fn (Get $get) => !$get('mail_disable'),
                    'default' => '',
                    'disable_toolbar' => true,
                ],
                [
                    'name' => 'mail_css',
                    'label' => 'Mail CSS',
                    'type' => 'markdown',
                    'required' => fn (Get $get) => !$get('mail_disable'),
                    'default' => '',
                    'disable_toolbar' => true,
                ],
            ],
            'tickets' => [
                [
                    'name' => 'ticket_departments',
                    'label' => 'Ticket Departments',
                    'type' => 'tags',
                    'default' => ['Support', 'Sales'],
                    'required' => true,
                    'database_type' => 'array',
                ],
                [
                    'name' => 'ticket_client_closing_disabled',
                    'label' => 'Disallow clients from closing tickets',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                ],
                // Email piping
                [
                    'name' => 'ticket_mail_piping',
                    'label' => 'Email Piping',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                    'live' => true,
                ],
                [
                    'name' => 'ticket_mail_host',
                    'label' => 'Email Host',
                    'type' => 'text',
                    'required' => fn (Get $get) => $get('ticket_mail_piping'),
                ],
                [
                    'name' => 'ticket_mail_port',
                    'label' => 'Email Port',
                    'type' => 'number',
                    'required' => fn (Get $get) => $get('ticket_mail_piping'),
                    'default' => 993,
                ],
                [
                    'name' => 'ticket_mail_email',
                    'label' => 'Email Address',
                    'type' => 'email',
                    'required' => fn (Get $get) => $get('ticket_mail_piping'),
                ],
                [
                    'name' => 'ticket_mail_password',
                    'label' => 'Email Password',
                    'type' => 'password',
                    'required' => fn (Get $get) => $get('ticket_mail_piping'),
                    'encrypted' => true,
                ],
            ],

            'cronjob' => [
                [
                    'name' => 'cronjob_time',
                    'label' => 'Cron Job Time',
                    'type' => 'time',
                    'default' => '00:00',
                    'required' => true,
                    'description' => 'Time the cron job should run daily (in 24 hour format, e.g. 14:00 for 2 PM).',
                ],
                [
                    'name' => 'cronjob_invoice',
                    'label' => 'Send invoice if due date is x days away',
                    'type' => 'number',
                    'default' => 7,
                    'required' => true,
                ],
                [
                    'name' => 'cronjob_invoice_reminder',
                    'label' => 'Send invoice reminder if due date is x days away',
                    'type' => 'number',
                    'default' => 3,
                    'required' => true,
                ],
                [
                    // Cancel order is pending for x days
                    'name' => 'cronjob_order_cancel',
                    'label' => 'Cancel order if pending for x days',
                    'type' => 'number',
                    'default' => 7,
                    'required' => true,
                ],
                [
                    'name' => 'cronjob_order_suspend',
                    'label' => 'Suspend server if invoice is x days overdue',
                    'type' => 'number',
                    'default' => 2,
                    'required' => true,
                ],
                [
                    'name' => 'cronjob_order_terminate',
                    'label' => 'Delete server if invoice is x days overdue (also cancels the invoice)',
                    'type' => 'number',
                    'default' => 14,
                    'required' => true,
                ],
                [
                    'name' => 'cronjob_delete_email_logs',
                    'label' => 'Delete email logs older than x days',
                    'type' => 'number',
                    'default' => 90,
                    'required' => true,
                ],
                [
                    'name' => 'cronjob_close_ticket',
                    'label' => 'Close tickets if no response for x days',
                    'type' => 'number',
                    'default' => 7,
                    'required' => true,
                ],
            ],
            'credits' => [
                [
                    'name' => 'credits_enabled',
                    'label' => 'Credits Enabled',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                ],
                [
                    'name' => 'credits_minimum_deposit',
                    'label' => 'Minimum Deposit',
                    'type' => 'number',
                    'default' => 5,
                    'required' => true,
                ],
                [
                    'name' => 'credits_maximum_deposit',
                    'label' => 'Maximum Deposit',
                    'type' => 'number',
                    'default' => 100,
                    'required' => true,
                ],
                [
                    'name' => 'credits_maximum_credit',
                    'label' => 'Maximum Credit',
                    'type' => 'number',
                    'default' => 300,
                    'required' => true,
                ],
                [
                    'name' => 'credits_auto_use',
                    'label' => 'Automatically use credits',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => true,
                    'description' => 'Automatically pay recurring invoices using available credits. (only pays if credits is more or equal to invoice amount)',
                ],
                [
                    // Enable credits give back if and service is upgraded or downgraded
                    'name' => 'credits_on_downgrade',
                    'label' => 'Enable credits on service downgrade',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => true,
                    'description' => 'Enable giving back credits to users when they downgrade their service. The credits given back will be the prorated difference between the old and new service based on the remaining time in the billing cycle.',
                ],
            ],
            'theme' => [
                [
                    'name' => 'theme',
                    'label' => 'Theme',
                    'default' => 'default',
                    'type' => 'select',
                    'required' => true,
                    // Read themes from themes directory
                    'options' => array_map('basename', glob(base_path('themes/*'), GLOB_ONLYDIR)),
                    'validation' => 'in:' . implode(',', array_map('basename', glob(base_path('themes/*'), GLOB_ONLYDIR))),
                ],
            ],
            'invoices' => [
                [
                    'name' => 'bill_to_text',
                    'label' => 'Bill To Text',
                    'type' => 'textarea',
                    'default' => '',
                ],
                [
                    'name' => 'invoice_number',
                    'label' => 'Invoice Number',
                    'type' => 'number',
                    'default' => 1,
                    'required' => false,
                    'description' => 'The next invoice number to use. This will be incremented automatically.',
                ],
                [
                    'name' => 'invoice_number_padding',
                    'label' => 'Invoice Number Padding',
                    'type' => 'number',
                    'default' => 1,
                    'required' => false,
                    'description' => 'Number of digits to use for invoice numbers. Example: 0001, 0002, etc.',
                ],
                [
                    'name' => 'invoice_number_format',
                    'label' => 'Invoice number format',
                    'type' => 'text',
                    'default' => 'INV-{number}',
                    'required' => false,
                    'description' => 'Format to use for invoice numbers. Use {number} to insert the zero padded number and use {year}, {month} and {day} placeholders to insert the current date. Example: INV-{year}-{month}-{day}-{number} or INV-{year}{number}. It must at least contain {number}.',
                    'validation' => 'regex:/{number}/',
                ],
                [
                    'name' => 'invoice_proforma',
                    'label' => 'Proforma Invoices',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                    'description' => 'Proforma invoices will not be assigned an official invoice number until payment is received and will be marked as "Proforma".',
                ],
                [
                    'name' => 'invoice_snapshot',
                    'label' => 'Invoice Snapshot',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => true,
                    'description' => 'Save a snapshot of important data (name, address, etc.) on the invoice when it is paid. This ensures that if someone changes their details later, old invoices will still have the correct information.',
                ],
            ],
            'other' => [
                [
                    'name' => 'gravatar_default',
                    'label' => 'Gravatar Default',
                    'description' => 'Default image to use when a user does not have a Gravatar. ',
                    'link' => 'https://docs.gravatar.com/general/images/#default-image',
                    'type' => 'select',
                    'options' => [
                        'mp' => 'Mystery Person',
                        'identicon' => 'Identicon',
                        'monsterid' => 'Monster',
                        'wavatar' => 'Wavatar',
                        'retro' => 'Retro',
                        'robohash' => 'Robohash',
                        'blank' => 'Blank',
                    ],
                    'default' => 'wavatar',
                ],
                [
                    'name' => 'default_currency',
                    'label' => 'Default Currency',
                    'type' => 'select',
                    'options' => $currencies,
                    'default' => 'USD',
                    'required' => true,
                ],
                [
                    'name' => 'registration_disabled',
                    'label' => 'Disable User Registration',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                    'description' => 'Only allow existing users to log in. This will hide the registration page and prevent new users from signing up.',
                ],
                [
                    'name' => 'tickets_disabled',
                    'label' => 'Disable Tickets',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                    'description' => 'Disable the ticket system. This will disable all client side ticket functionality, including the ability to create new tickets and view existing tickets.',
                ],
                [
                    'name' => 'pagination',
                    'label' => 'Pagination',
                    'type' => 'number',
                    'default' => 10,
                    'required' => true,
                    'description' => 'Number of items to show per page',
                ],
                [
                    'name' => 'debug',
                    'label' => 'Debug Mode',
                    'type' => 'checkbox',
                    'database_type' => 'boolean',
                    'default' => false,
                    'description' => 'Enable debug mode to log HTTP requests and errors',
                ],
            ],
        ];

        // Set theme settings
        $settings['theme'] = [...$settings['theme'], ...Theme::getSettings()];

        return $settings;
    }

    public static function tax(?User $user = null)
    {
        // Use once so the query is only run once
        return once(function () use ($user) {
            $user ??= Auth::user();
            // Get country from user properties
            $country = $user?->properties->where('key', 'country')->value('value') ?? null;

            // Change country to a two-letter country code if it's not already
            if ($country) {
                $country = array_search($country, config('app.countries')) ?: $country;
            }

            $taxRate = TaxRate::whereIn('country', [$country, 'all'])
                ->orderByRaw('country = ? desc', [$country])
                ->first();

            return $taxRate ?: 0;
        });
    }

    public static function settingsObject()
    {
        return (object) json_decode(json_encode(static::settings()));
    }

    public static function getSetting($key)
    {
        $setting = (object) collect(static::settings())->flatten(1)->firstWhere('name', $key);
        $setting->value = Setting::where('settingable_type', null)->where('key', $key)->value('value') ?? $setting->default ?? null;

        return $setting;
    }

    public static function getTelemetry()
    {
        try {
            $uuid = Setting::where('key', 'telemetry_uuid')->value('value');
        } catch (Exception $e) {
            $uuid = null;
        }
        if (is_null($uuid)) {
            $uuid = Uuid::uuid4()->toString();
            try {
                Setting::updateOrCreate(
                    ['key' => 'telemetry_uuid'],
                    ['value' => $uuid]
                );
            } catch (Exception $e) {
                // Avoid errors in workflows
            }
        }

        // Daily fixed time based on UUID
        $time = hexdec(str_replace('-', '', substr($uuid, 27))) % 1440;
        $hour = floor($time / 60);
        $minute = $time % 60;

        return compact('uuid', 'hour', 'minute');
    }

    public static function validateOrCreateVapidKeys(): bool
    {
        $publicKey = config('settings.vapid_public_key');
        $privateKey = config('settings.vapid_private_key');
        if ($publicKey && $privateKey && strlen($publicKey) > 80 && strlen($privateKey) > 40) {
            return true;
        }
        try {
            $vapid = VAPID::createVapidKeys();
            Setting::updateOrCreate(
                ['key' => 'vapid_public_key', 'encrypted' => true],
                ['value' => $vapid['publicKey']]
            );
            Setting::updateOrCreate(
                ['key' => 'vapid_private_key', 'encrypted' => true],
                ['value' => $vapid['privateKey']]
            );

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
