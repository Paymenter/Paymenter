<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Others\Announcements\Livewire\Announcements\Index;
use Paymenter\Extensions\Others\Announcements\Livewire\Announcements\Show;

Route::group(['middleware' => ['web']], function () {
    Route::get('/announcements', Index::class)->name('announcements.index');
    Route::get('/announcements/{announcement:slug}', Show::class)->name('announcements.show');
});
