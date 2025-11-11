<?php

namespace Paymenter\Extensions\Others\Announcements;

use App\Classes\Extension\Extension;
use App\Helpers\ExtensionHelper;
use App\Livewire\Auth\Register;
use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;
use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource;
use Paymenter\Extensions\Others\Announcements\Livewire\Announcements\Index;
use Paymenter\Extensions\Others\Announcements\Livewire\Announcements\Show;
use Paymenter\Extensions\Others\Announcements\Livewire\Announcements\Widget;
use Paymenter\Extensions\Others\Announcements\Models\Announcement;

class Announcements extends Extension
{
    public function getConfig($values = [])
    {
        // If announcement resource is not installed, return placeholder
        try {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('You can use this extension to display announcements on the client area. To create a new announcement, go to <a class="text-primary-600" href="' . AnnouncementResource::getUrl() . '">Announcements</a>.'),
                ],
            ];
        } catch (Exception $e) {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('You can use this extension to display announcements on the client area. You\'ll need to enable this extension above to get started.'),
                ],
            ];
        }
    }

    public function installed()
    {
        ExtensionHelper::runMigrations('extensions/Others/Announcements/database/migrations');
    }

    public function uninstalled()
    {
        // Rollback migrations
        ExtensionHelper::rollbackMigrations('extensions/Others/Announcements/database/migrations');
    }

    public function boot()
    {
        // Register routes
        require __DIR__ . '/routes/web.php';
        View::addNamespace('announcements', __DIR__ . '/resources/views');

        // Register livewire
        Livewire::component('announcements.index', Index::class);
        Livewire::component('announcements.show', Show::class);
        Livewire::component('announcements.widget', Widget::class);

        Gate::policy(Announcement::class, Policies\AnnouncementPolicy::class);

        Event::listen('navigation', function () {
            if (Announcement::where('is_active', true)->where('published_at', '<=', now())->count() == 0) {
                return;
            }

            return [
                'name' => 'Announcements',
                'route' => 'announcements.index',
                'icon' => 'ri-megaphone',
                'separator' => true,
                'children' => [],
            ];
        });

        Event::listen('permissions', function () {
            return [
                'admin.announcements.view' => 'View Announcements',
                'admin.announcements.create' => 'Create Announcements',
                'admin.announcements.update' => 'Update Announcements',
                'admin.announcements.delete' => 'Delete Announcements',
            ];
        });

        Event::listen('pages.home', function () {
            return [
                'view' => view('announcements::index', [
                    'announcements' => Announcement::where('is_active', true)->where('published_at', '<=', now())->orderBy('published_at', 'desc')->get(),
                ]),
            ];
        });

        Event::listen('pages.dashboard', function () {
            return [
                'view' => view('announcements::widget', [
                    'announcements' => Announcement::where('is_active', true)
                        ->where('published_at', '<=', now())
                        ->orderBy('published_at', 'desc')
                        ->get(),
                ]),
            ];
        });
    }
}
