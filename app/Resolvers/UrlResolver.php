<?php

namespace App\Resolvers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

class UrlResolver implements Resolver
{
    /**
     * {@inheritdoc}
     */
    public static function resolve(Auditable $auditable): string
    {
        if (App::runningInConsole()) {
            return 'console';
        }

        // Just the full URL without query strings
        return Request::livewireUrl();
    }
}
