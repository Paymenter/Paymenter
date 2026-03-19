<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\SettingsProvider;
use SocialiteProviders\Manager\ServiceProvider;

return [
    AppServiceProvider::class,
    SettingsProvider::class,
    AdminPanelProvider::class,
    ServiceProvider::class,
];
