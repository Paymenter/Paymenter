<?php

namespace Tests\Feature;

use App\Models\Extension;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Paymenter\Extensions\Others\Announcements\Announcements;
use Paymenter\Extensions\Others\Announcements\Models\Announcement;
use Tests\TestCase;

class AnnouncementWidgetTest extends TestCase
{
    // The extension's tables live outside the core migrations, and creating them inside
    // RefreshDatabase's transaction would commit it.
    use DatabaseMigrations;

    protected function afterRefreshingDatabase()
    {
        $this->artisan('migrate', ['--path' => 'extensions/Others/Announcements/database/migrations']);

        Extension::create(['name' => 'Announcements', 'extension' => 'Announcements', 'type' => 'other', 'enabled' => true]);

        (new Announcements)->boot();

        // Otherwise the next application to boot would load the extension without its tables
        $this->beforeApplicationDestroyed(fn () => Extension::query()->forceDelete());
    }

    private function createAnnouncement(int $daysAgo): Announcement
    {
        return Announcement::create([
            'title' => 'Announcement ' . $daysAgo,
            'slug' => 'announcement-' . $daysAgo,
            'description' => 'Description',
            'content' => '<p>Content</p>',
            'is_active' => true,
            'published_at' => now()->subDays($daysAgo),
        ]);
    }

    public function test_dashboard_shows_the_latest_five_announcements(): void
    {
        foreach (range(1, 7) as $daysAgo) {
            $this->createAnnouncement($daysAgo);
        }

        $announcements = collect(event('pages.dashboard'))->filter()->first()['view']->getData()['announcements'];

        $this->assertSame(
            ['announcement-1', 'announcement-2', 'announcement-3', 'announcement-4', 'announcement-5'],
            $announcements->pluck('slug')->all()
        );
    }

    public function test_navigation_item_depends_on_published_announcements(): void
    {
        $this->assertSame([], array_filter(event('navigation')));

        $this->createAnnouncement(1);

        $this->assertSame('announcements.index', collect(event('navigation'))->filter()->first()['route']);
    }
}
