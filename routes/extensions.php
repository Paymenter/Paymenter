<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

Route::group(['prefix' => 'extensions'], function () {
    $extensions = glob(app_path() . '/Extensions/**/*', GLOB_ONLYDIR);
    foreach ($extensions as $extension) {
        $routesFile = $extension . '/routes.php';
        if (file_exists($routesFile)) {
            if (file_exists($extension . '/views')) View::addNamespace(basename($extension), $extension . '/views');
            include_once $routesFile;
        }
    }
});
