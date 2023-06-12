<?php

use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

include_once __DIR__ . '/index.php';

Route::post('/proxmox/status/{id}', function (Request $request, OrderProduct $id) {
    return Proxmox_status($request, $id);
})->name('extensions.proxmox.status');