<?php

namespace App\Providers\Filament;

use App\Providers\SettingsProvider;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Notifications\Livewire\Notifications;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Filament loads before the settings provider, so we need to load the settings here
        SettingsProvider::getSettings();

        Notifications::alignment(Alignment::Center);

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->favicon('/logo.webp')
            ->discoverResources(in: app_path('Admin/Resources'), for: 'App\\Admin\\Resources')
            ->discoverPages(in: app_path('Admin/Pages'), for: 'App\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Admin/Widgets'), for: 'App\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_END ,
                fn (): string => Blade::render('<x-admin-footer />'),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->theme(asset('css/filament/admin/theme.css'))
            ->authMiddleware([
                Authenticate::class,
            ]);

    }
}
