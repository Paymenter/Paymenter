<?php

return [
    // Split settings into groups (only used in the settings page for organization)
    'general' => [
        [
            'name' => 'timezone',
            'label' => 'Timezone',
            'type' => 'select',
            'options' => [
                // Read timezones from PHP
                DateTimeZone::listIdentifiers(DateTimeZone::ALL),
            ],
            'default' => 'UTC',
            'required' => true,
        ],
        [
            'name' => 'app_locale',
            'label' => 'App Locale',
            'default' => 'en',
            'type' => 'select',
            'options' => [
                // Read languages from resources/lang directory
                array_map('basename', glob(resource_path('lang/*'), GLOB_ONLYDIR)),
            ],
            'required' => true,
        ],
        [
            'name' => 'app_url',
            'label' => 'App URL',
            'default' => 'http://localhost',
            'type' => 'text',
            'required' => true,
        ],
        [
            'name' => 'theme',
            'default' => 'default',
            'type' => 'select',
            'options' => [
                // Read themes from themes directory
                array_map('basename', glob(base_path('themes/*'), GLOB_ONLYDIR)),
            ],
        ],
        [
            'name' => 'logo',
            'label' => 'Logo',
            'type' => 'file',
            'required' => false,
            'default' => 'https://paymenter.org/image/textlogo.png',
            'accept' => 'image/*',
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
            'default' => 'turnstile',
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
        ]
    ]
];
