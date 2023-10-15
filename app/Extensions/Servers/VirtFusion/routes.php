<?php

use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/virtfusion/login/{id}', [App\Extensions\Servers\VirtFusion\VirtFusion::class, 'login'])->name('extensions.virtfusion.login');