<?php

use Illuminate\Support\Facades\Route;

Route::post('/proxmox/status/{product}', [App\Extensions\Servers\Proxmox\Proxmox::class, 'status'])->name('extensions.proxmox.status');

Route::post('/proxmox/configure/{product}', [App\Extensions\Servers\Proxmox\Proxmox::class, 'configure'])->name('extensions.proxmox.configure');