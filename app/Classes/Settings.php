<?php

namespace App\Classes;

use App\Models\Setting;


class Settings
{
    public static function settings()
    {
        try {
            // Only code is needed
            $currencies = \App\Models\Currency::pluck('code')->toArray();
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
                    'options' => glob(resource_path('lang/*'), GLOB_ONLYDIR) ? array_map('basename', glob(resource_path('lang/*'), GLOB_ONLYDIR)) : ['en'],
                    'required' => true,
                    'validation' => 'in:' . implode(',', glob(resource_path('lang/*'), GLOB_ONLYDIR) ? array_map('basename', glob(resource_path('lang/*'), GLOB_ONLYDIR)) : ['en']),
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
                    'file_name' => 'logo.webp'
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
                    'name' => 'show_in_footer',
                    'label' => 'Show in Footer',
                    'type' => 'checkbox',
                    'default' => true,
                ],
                [
                    'name' => 'company_name',
                    'label' => 'Company Name',
                    'type' => 'text',
                    'override' => 'app.name',
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
                ]
            ],

            'other' => [
                [
                    'name' => 'optional_fields',
                    'label' => 'Optional Fields',
                    'type' => 'select',
                    'multiple' => true,
                    'options' => [
                        'first_name' => 'First Name',
                        'last_name' => 'Last Name',
                        'phone' => 'Phone',
                        'address' => 'Address',
                        'address2' => 'Address 2',
                        'city' => 'City',
                        'state' => 'State',
                        'zip' => 'Zip',
                        'country' => 'Country',
                    ],
                    'default' => ["address2", "phone"],
                    'required' => false,
                    'database_type' => 'array',
                ],
                [
                    'name' => 'locked_fields',
                    'label' => 'Locked Fields',
                    'type' => 'select',
                    'multiple' => true,
                    'options' => [
                        'first_name' => 'First Name',
                        'last_name' => 'Last Name',
                        'phone' => 'Phone',
                        'address' => 'Address',
                        'address2' => 'Address 2',
                        'city' => 'City',
                        'state' => 'State',
                        'zip' => 'Zip',
                        'country' => 'Country',
                    ],
                    'database_type' => 'array',
                ],
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
            ]
        ];


        // Set theme settings
        $settings['theme'] = \App\Classes\Theme::getSettings();

        return $settings;
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
