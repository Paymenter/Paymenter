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
            'name' => 'primary',
            'label' => 'Primary - Brand Color (Light)',
            'type' => 'color',
            'default' => 'hsl(229, 100%, 64%)',
        ],
        [
            'name' => 'secondary',
            'label' => 'Secondary - Brand Color (Light)',
            'type' => 'color',
            'default' => 'hsl(237, 33%, 60%)',
        ],
        [
            'name' => 'neutral',
            'label' => 'Borders, Accents... (Light)',
            'type' => 'color',
            'default' => 'hsl(220, 25%, 85%)',
        ],
        [
            'name' => 'base',
            'label' => 'Base - Text Color (Light)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 0%)',
        ],
        [
            'name' => 'muted',
            'label' => 'Muted - Text Color (Light)',
            'type' => 'color',
            'default' => 'hsl(220, 28%, 25%)',
        ],
        [
            'name' => 'inverted',
            'label' => 'Inverted - Text Color (Light)',
            'type' => 'color',
            'default' => 'hsl(100, 100%, 100%)',
        ],
        [
            'name' => 'background',
            'label' => 'Background - Color (Light)',
            'type' => 'color',
            'default' => 'hsl(100, 100%, 100%)',
        ],
        [
            'name' => 'background-secondary',
            'label' => 'Background - Secondary Color (Light)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 97%)',
        ],
        [
            'name' => 'dark-primary',
            'label' => 'Primary - Brand Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(229, 100%, 64%)',
        ],
        [
            'name' => 'dark-secondary',
            'label' => 'Secondary - Brand Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(237, 33%, 60%)',
        ],
        [
            'name' => 'dark-neutral',
            'label' => 'Borders, Accents... (Dark)',
            'type' => 'color',
            'default' => 'hsl(220, 25%, 29%)',
        ],
        [
            'name' => 'dark-base',
            'label' => 'Base - Text Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(100, 100%, 100%)',
        ],
        [
            'name' => 'dark-muted',
            'label' => 'Muted - Text Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(220, 28%, 25%)',
        ],
        [
            'name' => 'dark-inverted',
            'label' => 'Inverted - Text Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(220, 14%, 60%)',
        ],
        [
            'name' => 'dark-background',
            'label' => 'Background - Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(221, 39%, 11%)',
        ],
        [
            'name' => 'dark-background-secondary',
            'label' => 'Background - Secondary Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(217, 33%, 16%)',
        ],
    ],
];
