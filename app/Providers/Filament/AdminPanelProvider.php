<?php

namespace App\Providers\Filament;

use App\Http\Middleware\ImpersonateMiddleware;
use App\Models\Extension;
use App\Providers\SettingsProvider;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Notifications\Livewire\Notifications;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Filament loads before the settings provider, so we need to load the settings here
        SettingsProvider::getSettings();

        Notifications::alignment(Alignment::Center);

        $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->favicon(config('settings.logo') ? Storage::url(config('settings.logo')) : null)
            ->discoverResources(in: app_path('Admin/Resources'), for: 'App\\Admin\\Resources')
            ->discoverPages(in: app_path('Admin/Pages'), for: 'App\\Admin\\Pages')
            ->discoverClusters(in: app_path('Admin/Clusters'), for: 'App\\Admin\\Clusters')
            ->userMenuItems([
                MenuItem::make()
                    ->label('Exit Admin')
                    ->url('/')
                    ->icon('heroicon-s-arrow-uturn-left')
                    ->sort(24),
            ])
            ->discoverWidgets(in: app_path('Admin/Widgets'), for: 'App\\Admin\\Widgets')
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_END,
                fn (): string => Blade::render('<x-admin-footer />'),
            )
            ->navigationGroups([
                'Administration',
                'Configuration',
                'Extensions',
                'System',
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ImpersonateMiddleware::class,
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

        try {
            foreach (collect(Extension::where(function ($query) {
                $query->where('enabled', true)->orWhere('type', 'server')->orWhere('type', 'gateway');
            })->get())->unique('extension') as $extension) {
                $panel->discoverResources(in: base_path('extensions' . '/' . $extension->path . '/Admin/Resources'), for: $extension->namespace . '\\Admin\\Resources');
                $panel->discoverPages(in: base_path('extensions' . '/' . $extension->path . '/Admin/Pages'), for: $extension->namespace . '\\Admin\\Pages');
                $panel->discoverClusters(in: base_path('extensions' . '/' . $extension->path . '/Admin/Clusters'), for: $extension->namespace . '\\Admin\\Clusters');
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        return $panel;
    }
}
