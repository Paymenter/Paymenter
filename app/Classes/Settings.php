<?php

namespace App\Classes;

use App\Models\Setting;
use App\Models\TaxRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Ramsey\Uuid\Uuid;

class Settings
{
    public static function settings()
    {
        try {
            // Only code is needed
            $currencies = once(function () {
                return \App\Models\Currency::pluck('code')->toArray();
            });
        } catch (\Exception $e) {
            $currencies = [];
        }
        $settings = [
            // Split settings into groups (only used in the settings page for organization)
            'general' => [
                [
                    'name' => 'timezone',
                    'label' => 'Timezone',
                    'type' => 'select',
                    // Read timezones from PHP
                    'options' => \DateTimeZone::listIdentifiers(\DateTimeZone::ALL),
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
                    'label' => 'Logo',
                    'type' => 'file',
                    'required' => false,
                    'accept' => ['image/*'],
                    'file_name' => 'logo.webp',
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

            'company-details' => [
                [
                    'name' => 'company_name',
                    'label' => 'Company Name',
                    'type' => 'text',
                    'override' => 'app.name',
                    'default' => 'Paymenter',
                ],
                [
                    'name' => 'company_email',
                    'label' => 'Company Email',
                    'type' => 'email',
                ],
                [
                    'name' => 'company_phone',
                    'label' => 'Company Phone',
                    'type' => 'text',
                ],
                [
                    'name' => 'company_address',
                    'label' => 'Company Address',
                    'type' => 'text',
                ],
                [
                    'name' => 'company_address2',
                    'label' => 'Company Address 2',
                    'type' => 'text',
                ],
                [
                    'name' => 'company_city',
                    'label' => 'Company City',
                    'type' => 'text',
                ],
                [
                    'name' => 'company_state',
                    'label' => 'Company State',
                    'type' => 'text',
                ],
                [
                    'name' => 'company_zip',
                    'label' => 'Company Zip',
                    'type' => 'text',
                ],
                [
                    'name' => 'company_country',
                    'label' => 'Company Country',
                    'type' => 'select',
                    'options' => array_merge(['' => 'None'], config('app.countries')),
                ],
                [
                    'name' => 'company_tax_id',
                    'label' => 'Company Tax ID',
                    'type' => 'text',
                ],
                [
                    'name' => 'company_id',
                    'label' => 'Company ID',
                    'type' => 'text',
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
                    'required' => false,
                    'override' => 'mail.mailers.smtp.host',
                ],
                [
                    'name' => 'mail_port',
                    'label' => 'Mail Port',
                    'type' => 'text',
                    'required' => false,
                    'override' => 'mail.mailers.smtp.port',
                ],
                [
                    'name' => 'mail_username',
                    'label' => 'Mail Username',
                    'type' => 'text',
                    'required' => false,
                    'override' => 'mail.mailers.smtp.username',
                ],
                [
                    'name' => 'mail_password',
                    'label' => 'Mail Password',
                    'type' => 'password',
                    'required' => false,
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
                    'required' => false,
                    'override' => 'mail.from.address',
                ],
                [
                    'name' => 'mail_from_name',
                    'label' => 'Mail From Name',
                    'type' => 'text',
                    'required' => false,
                    'override' => 'mail.from.name',
                ],

                // Theming
                [
                    'name' => 'mail_header',
                    'label' => 'Header',
                    'type' => 'markdown',
                    'required' => false,
                    'default' => '',
                    'disable_toolbar' => true,
                ],
                [
                    'name' => 'mail_footer',
                    'label' => 'Footer',
                    'type' => 'markdown',
                    'required' => false,
                    'default' => '',
                    'disable_toolbar' => true,
                ],
                [
                    'name' => 'mail_css',
                    'label' => 'Mail CSS',
                    'type' => 'markdown',
                    'required' => false,
                    'default' => '',
                    'disable_toolbar' => true,
                ],
            ],
            'cronjob' => [
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
                    'default' => '{number}',
                    'required' => false,
                    'description' => 'Format to use for invoice numbers. Use {number} to insert the zero padded number and use {year}, {month} and {day} placeholders to insert the current date. Example: INV-{year}-{month}-{day}-{number} or INV-{year}{number}. It must at least contain {number}.',
                    'validation' => 'regex:/{number}/',
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
                    'name' => 'ticket_departments',
                    'label' => 'Ticket Departments',
                    'type' => 'tags',
                    'default' => ['Support', 'Sales'],
                    'required' => true,
                    'database_type' => 'array',
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
        $settings['theme'] = [...$settings['theme'], ...\App\Classes\Theme::getSettings()];

        return $settings;
    }

    public static function tax()
    {
        // Use once so the query is only run once
        return once(function () {
            $country = Auth::user()?->properties()->where('key', 'country')->value('value') ?? null;

            // Change country to a two-letter country code if it's not already
            if ($country) {
                $country = array_search($country, config('app.countries')) ?: $country;
            }

            if ($taxRate = TaxRate::where('country', $country)->first()) {
                return $taxRate;
            } elseif ($taxRate = TaxRate::where('country', 'all')->first()) {
                return $taxRate;
            }

            return 0;
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
        } catch (\Exception $e) {
            $uuid = null;
        }
        if (is_null($uuid)) {
            $uuid = Uuid::uuid4()->toString();
            try {
                Setting::updateOrCreate(
                    ['key' => 'telemetry_uuid'],
                    ['value' => $uuid]
                );
            } catch (\Exception $e) {
                // Avoid errors in workflows
            }
        }

        // Daily fixed time based on UUID
        $time = hexdec(str_replace('-', '', substr($uuid, 27))) % 1440;
        $hour = floor($time / 60);
        $minute = $time % 60;

        return compact('uuid', 'hour', 'minute');
    }
}
