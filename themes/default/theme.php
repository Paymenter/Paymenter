<?php

return [
    'name' => 'Default',
    'author' => 'Paymenter',
    'url' => 'https://paymenter.org',

    'settings' => [
        [
            'name' => 'direct_checkout',
            'label' => 'Direct Checkout',
            'type' => 'checkbox',
            'default' => false,
            'description' => 'Don\'t show the product overview page, go directly to the checkout page',
        ],
        [
            'name' => 'home_page_text',
            'label' => 'Home Page Text',
            'type' => 'markdown',
            'default' => 'Welcome to Paymenter!',
        ],
        [
            'name' => 'primary-900',
            'label' => 'Background Color',
            'type' => 'color',
            'default' => '#111827',
        ],
        [
            'name' => 'primary-800',
            'label' => 'Background 2 Color',
            'type' => 'color',
            'default' => '#1F2937',
        ],
        [
            'name' => 'secondary',
            'label' => 'Button/Focus Color',
            'type' => 'color',
            'default' => '#5270FD',
        ],
    ],
];
