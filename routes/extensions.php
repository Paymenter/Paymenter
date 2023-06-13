<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

Route::group(['prefix' => 'extensions'], function () {
    $extensions = glob(app_path() . '/Extensions/**/*', GLOB_ONLYDIR);
    foreach ($extensions as $extension) {
        View::addNamespace(basename($extension), $extension . '/views');
        $routesFile = $extension . '/routes.php';
        if (file_exists($routesFile)) {
            include_once $routesFile;
        }
    }
});
