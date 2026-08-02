<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Others\CyberpunkTheme\Livewire\Community;
use Paymenter\Extensions\Others\CyberpunkTheme\Livewire\CustomPage;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;

Route::group(['middleware' => ['web']], function () {
    // Páginas personalizadas creadas desde el panel
    Route::get('/p/{slug}', CustomPage::class)
        ->where('slug', '[A-Za-z0-9_-]+')
        ->name('cyberpunk.page');

    // Comunidad (el slug se configura desde el panel)
    if (Config::themeBool('community_enabled', true)) {
        Route::get('/' . Config::communitySlug(), Community::class)->name('cyberpunk.community');
    }
});
