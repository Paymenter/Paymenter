<?php

namespace App\Resolvers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class UrlResolver implements \OwenIt\Auditing\Contracts\Resolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(\OwenIt\Auditing\Contracts\Auditable $auditable): string
    {
        if (App::runningInConsole()) {
            return 'console';
        }

        // Just the full URL without query strings
        return Request::livewireUrl();
    }
}
