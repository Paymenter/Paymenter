<?php

return [
    'theme_color' => env('APP_THEME_COLOR', '#ff7a00'),

    'socials' => [
        'discord' => env('FOOTER_DISCORD'),
        'github' => env('FOOTER_GITHUB'),
        'twitter' => env('FOOTER_TWITTER'),
    ],

    'pages' => [
        [
            'name' => env('FOOTER_PAGE_1_NAME'),
            'link' => env('FOOTER_PAGE_1_LINK'),
        ],
        [
            'name' => env('FOOTER_PAGE_2_NAME'),
            'link' => env('FOOTER_PAGE_2_LINK'),
        ],
        [
            'name' => env('FOOTER_PAGE_3_NAME'),
            'link' => env('FOOTER_PAGE_3_LINK'),
        ],
    ]
];