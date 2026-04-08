<?php

return [
    'max_items' => env('CART_MAX_ITEMS', 20),

    'rate_limit' => [
        'max_attempts' => env('CART_ADD_RATE_LIMIT_MAX_ATTEMPTS', 10),
        'decay_minutes' => env('CART_ADD_RATE_LIMIT_DECAY_MINUTES', 1),
    ],
];
