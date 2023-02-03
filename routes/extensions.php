<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'extensions'], function () {
    $extensions = glob(app_path() . '/Extensions/Gateways/*', GLOB_ONLYDIR);
    foreach ($extensions as $extension) {
        $routesFile = $extension . '/routes.php';
        if (file_exists($routesFile)) {
            include_once $routesFile;
        }
    }
});
