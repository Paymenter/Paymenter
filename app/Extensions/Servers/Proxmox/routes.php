<?php

use Illuminate\Support\Facades\Route;

Route::post('/proxmox/status/{id}', [App\Extensions\Servers\Proxmox\Proxmox::class, 'status'])->name('extensions.proxmox.status');

Route::post('/proxmox/configure/{id}', [App\Extensions\Servers\Proxmox\Proxmox::class, 'configure'])->name('extensions.proxmox.configure');