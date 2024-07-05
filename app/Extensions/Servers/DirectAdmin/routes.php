<?php

use Illuminate\Support\Facades\Route;

Route::post('/directadmin/login/{product}', [App\Extensions\Servers\DirectAdmin\DirectAdmin::class, 'login'])->name('extensions.directadmin.login');

Route::post('/directadmin/resetPwd/{product}', [App\Extensions\Servers\DirectAdmin\DirectAdmin::class, 'resetPwd'])->name('extensions.directadmin.resetPwd');
