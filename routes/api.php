<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Get all extensions from app/extensions
$extensions = glob(app_path() . '/Extensions/Gateways/*', GLOB_ONLYDIR);
foreach ($extensions as $extension) {
    $extensionName = basename($extension);
    $routesFile = $extension . '/routes.php';
    if (file_exists($routesFile)) {
        require $routesFile;
    }
}
