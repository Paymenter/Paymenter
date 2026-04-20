<?php

return [
    'name' => 'TropVPN',
    'author' => 'TropVPN',
    'url' => 'https://tropvpn.com',
    'settings' => [
        [
            'name' => 'direct_checkout',
            'label' => 'Direct Checkout',
            'type' => 'checkbox',
            'default' => false,
            'database_type' => 'boolean',
            'description' => 'Go directly to checkout, skipping the product overview page.',
        ],
        [
            'name' => 'show_category_description',
            'label' => 'Show Category Description',
            'type' => 'checkbox',
            'default' => true,
            'database_type' => 'boolean',
            'description' => 'Show the category description on the homepage/product listing.',
        ],
        [
            'name' => 'logo_display',
            'label' => 'Logo Display',
            'type' => 'select',
            'options' => [
                'logo-only' => 'Logo only',
                'logo-and-name' => 'Logo and Name',
            ],
            'default' => 'logo-and-name',
        ],
        [
            'name' => 'home_page_text',
            'label' => 'Home Page Text',
            'type' => 'markdown',
            'default' => 'Choose a plan and get connected in minutes.',
        ],

        // ── Light Mode Colors ──────────────────────────────────────────
        [
            'name' => 'primary',
            'label' => 'Primary Color (Light)',
            'type' => 'color',
            'default' => 'hsl(229, 80%, 55%)',
        ],
        [
            'name' => 'secondary',
            'label' => 'Secondary Color (Light)',
            'type' => 'color',
            'default' => 'hsl(255, 60%, 55%)',
        ],
        [
            'name' => 'neutral',
            'label' => 'Neutral / Border Color (Light)',
            'type' => 'color',
            'default' => 'hsl(235, 20%, 85%)',
        ],
        [
            'name' => 'base',
            'label' => 'Base Text Color (Light)',
            'type' => 'color',
            'default' => 'hsl(235, 15%, 10%)',
        ],
        [
            'name' => 'muted',
            'label' => 'Muted Text Color (Light)',
            'type' => 'color',
            'default' => 'hsl(235, 10%, 45%)',
        ],
        [
            'name' => 'inverted',
            'label' => 'Inverted Text Color (Light)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 100%)',
        ],
        [
            'name' => 'background',
            'label' => 'Background Color (Light)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 100%)',
        ],
        [
            'name' => 'background-secondary',
            'label' => 'Background Secondary Color (Light)',
            'type' => 'color',
            'default' => 'hsl(235, 20%, 97%)',
        ],

        // ── Dark Mode Colors ───────────────────────────────────────────
        [
            'name' => 'dark-primary',
            'label' => 'Primary Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(229, 80%, 62%)',
        ],
        [
            'name' => 'dark-secondary',
            'label' => 'Secondary Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(255, 60%, 60%)',
        ],
        [
            'name' => 'dark-neutral',
            'label' => 'Neutral / Border Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(235, 20%, 18%)',
        ],
        [
            'name' => 'dark-base',
            'label' => 'Base Text Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 98%)',
        ],
        [
            'name' => 'dark-muted',
            'label' => 'Muted Text Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(235, 10%, 55%)',
        ],
        [
            'name' => 'dark-inverted',
            'label' => 'Inverted Text Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(0, 0%, 100%)',
        ],
        [
            'name' => 'dark-background',
            'label' => 'Background Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(235, 25%, 8%)',
        ],
        [
            'name' => 'dark-background-secondary',
            'label' => 'Background Secondary Color (Dark)',
            'type' => 'color',
            'default' => 'hsl(235, 20%, 11%)',
        ],
    ],
];
