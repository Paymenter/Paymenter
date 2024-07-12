<?php

// Read app/Extensions directory

use Illuminate\Support\Facades\Route;

// Assign routes to extensions
$availableTypes = array_diff(scandir(app_path('Extensions')), ['..', '.']);
// Prefix routes with /{type}/{extension} (e.g. /gateways/stripe/webhook)
Route::prefix('extensions')->name('extensions.')->group(function () use ($availableTypes) {
    foreach ($availableTypes as $type) {
        Route::prefix(strtolower($type))->name(strtolower($type) . '.')->group(function () use ($type) {
            $availableExtensions = array_diff(scandir(app_path('Extensions/' . $type)), ['..', '.']);
            foreach ($availableExtensions as $extension) {
                $routes = app_path('Extensions/' . $type . '/' . $extension . '/routes.php');
                if (file_exists($routes)) {
                    Route::prefix(strtolower($extension))->name(strtolower($extension) . '.')->group(function () use ($routes) {
                        require $routes;
                    });
                }
            }
        });
    }
});
