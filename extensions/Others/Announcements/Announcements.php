<?php

namespace Paymenter\Extensions\Others\Announcements;

use App\Classes\Extension\Extension;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource;
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
        } catch (\Exception $e) {
            return [
                [
                    'name' => 'Notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('You can use this extension to display announcements on the client area. You\'ll need to enable this extension above to get started.'),
                ],
            ];
        }
    }

    public function enabled()
    {
        // Run migrations
        Artisan::call('migrate', ['--path' => 'extensions/Others/Announcements/database/migrations/2024_10_19_095356_create_ext_announcements_table.php']);
    }

    public function boot()
    {
        // Register routes
        require __DIR__ . '/routes/web.php';
        View::addNamespace('announcements', __DIR__ . '/resources/views');

        Event::listen('navigation', function () {
            if (Announcement::where('is_active', true)->where('published_at', '<=', now())->count() == 0) {
                return;
            }
            return [
                'name' => 'Announcements',
                'route' => 'announcements.index',
            ];
        });

        Event::listen('pages.home', function () {
            return [
                'view' => view('announcements::index', [
                    'announcements' => Announcement::where('is_active', true)->where('published_at', '<=', now())->orderBy('published_at', 'desc')->get(),
                ]),
            ];
        });
    }
}
