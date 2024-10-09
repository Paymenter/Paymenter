<?php

// Read app/Extensions directory

use Illuminate\Support\Facades\Route;

// Assign routes to extensions
$availableTypes = array_diff(scandir(base_path('extensions')), ['..', '.']);
// Prefix routes with /{type}/{extension} (e.g. /gateways/stripe/webhook)
Route::prefix('extensions')->name('extensions.')->group(function () use ($availableTypes) {
    foreach ($availableTypes as $type) {
        Route::prefix(strtolower($type))->name(strtolower($type) . '.')->group(function () use ($type) {
            $availableExtensions = array_diff(scandir(base_path('extensions/' . $type)), ['..', '.']);
            foreach ($availableExtensions as $extension) {
                $routes = base_path('extensions/' . $type . '/' . $extension . '/routes.php');
                if (file_exists($routes)) {
                    Route::prefix(strtolower($extension))->name(strtolower($extension) . '.')->group(function () use ($routes) {
                        require $routes;
                    });
                }
            }
        });
    }
});
