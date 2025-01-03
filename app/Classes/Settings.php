<?php

namespace App\Classes;

use App\Models\Setting;
use App\Models\TaxRate;
use Illuminate\Support\Facades\Auth;

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
                    'label' => 'App Language',
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
                    'name' => 'theme',
                    'default' => 'default',
                    'type' => 'select',
                    // Read themes from themes directory
                    'options' => array_map('basename', glob(base_path('themes/*'), GLOB_ONLYDIR)),
                    'validation' => 'in:' . implode(',', array_map('basename', glob(base_path('themes/*'), GLOB_ONLYDIR))),
                ],
                [
                    'name' => 'logo',
                    'label' => 'Logo',
                    'type' => 'file',
                    'required' => false,
                    'accept' => ['image/*'],
                    'file_name' => 'logo.webp',
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
                    'default' => '0x4AAAAAAAC-bTN5KkqiyxNM',
                ],
                [
                    'name' => 'captcha_secret',
                    'label' => 'Captcha Secret',
                    'type' => 'text',
                    'required' => false,
                    'default' => '0x4AAAAAAAC-baD1IX6FMxXxEduRXcmCtuM',
                ],
            ],

            'social-login' => [
                [
                    'name' => 'oauth_google',
                    'label' => 'Google Enabled',
                    'type' => 'checkbox',
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
                    'type' => 'checkbox',
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
                    'description' => 'Be sure to enable the OAuth2 redirect URL in your Discord application settings. Point it to: ',
                    'type' => 'checkbox',
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
            ],
            'tax' => [
                [
                    'name' => 'tax_enabled',
                    'label' => 'Tax Enabled',
                    'type' => 'checkbox',
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
                    'default' => true,
                ],
                [
                    'name' => 'mail_must_verify',
                    'label' => 'Users must verify email before buying',
                    'type' => 'checkbox',
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
                    'name' => 'cronjob_cancel',
                    'label' => 'Cancel order if pending for x days',
                    'type' => 'number',
                    'default' => 7,
                    'required' => true,
                ],
                [
                    'name' => 'cronjob_suspend',
                    'label' => 'Suspend server if invoice is x days overdue',
                    'type' => 'number',
                    'default' => 0,
                    'required' => true,
                ],
                [
                    'name' => 'cronjob_delete',
                    'label' => 'Delete server if invoice is x days overdue (also cancels the invoice)',
                    'type' => 'number',
                    'default' => 7,
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
                    'name' => 'close_tickets',
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
                ]
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
                    'name' => 'pagination',
                    'label' => 'Pagination',
                    'type' => 'number',
                    'default' => 10,
                    'required' => true,
                    'description' => 'Number of items to show per page',
                ],
            ],
        ];

        // Set theme settings
        $settings['theme'] = \App\Classes\Theme::getSettings();

        return $settings;
    }

    public static function tax()
    {
        $country = Auth::user()->country ?? null;

        // Use once so the query is only run once
        return once(function () use ($country) {
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
}
