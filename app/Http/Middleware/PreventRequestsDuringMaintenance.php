<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Admin panel is always reachable — admins use the bypass URL for frontend.
     */
    protected $except = [
        'admin',
        'admin/*',
    ];
}