<?php

namespace Paymenter\Extensions\Others\Affiliates\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AffiliateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Check if request contains `ref` query parameter
        if (request()->has('ref')) {
            Log::info("AffiliateServiceProvider: Affiliate code hit!", [
                'code' => request('ref')
            ]);
        }
    }
}
