<?php

namespace Tests\Feature;

use App\Models\Extension;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;
use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource\Pages\CreateAnnouncement;
use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource\Pages\EditAnnouncement;
use Paymenter\Extensions\Others\Announcements\Models\Announcement;
use Tests\TestCase;

class AnnouncementSlugTest extends TestCase
{
    // The extension's tables live outside the core migrations, and creating them inside
    // RefreshDatabase's transaction would commit it.
    use DatabaseMigrations;

    protected function afterRefreshingDatabase()
    {
        $this->artisan('migrate', ['--path' => 'extensions/Others/Announcements/database/migrations']);

        Extension::create(['name' => 'Announcements', 'extension' => 'Announcements', 'type' => 'other', 'enabled' => true]);

        // Boot again so the admin panel discovers the extension's resource
        $this->refreshApplication();

        // Otherwise the next application to boot would load the extension without its tables
        $this->beforeApplicationDestroyed(fn () => Extension::query()->forceDelete());

        $this->actingAs(User::factory()->create(['role_id' => 1]));
        Filament::setCurrentPanel('admin');
    }

    private function createAnnouncement(string $slug): Announcement
    {
        return Announcement::create([
            'title' => 'Maintenance',
            'slug' => $slug,
            'description' => 'Scheduled maintenance',
            'content' => '<p>Details</p>',
            'is_active' => true,
            'published_at' => now(),
        ]);
    }

    public function test_duplicate_slug_is_a_validation_error(): void
    {
        $this->createAnnouncement('maintenance');

        Livewire::test(CreateAnnouncement::class)
            ->fillForm([
                'title' => 'Maintenance',
                'slug' => 'maintenance',
                'description' => 'Another one',
                'content' => '<p>Details</p>',
                'published_at' => now(),
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);

        $this->assertSame(1, Announcement::count());
    }

    public function test_slug_with_a_slash_is_refused(): void
    {
        Livewire::test(CreateAnnouncement::class)
            ->fillForm([
                'title' => 'Maintenance',
                'slug' => 'maintenance/october',
                'description' => 'Scheduled maintenance',
                'content' => '<p>Details</p>',
                'published_at' => now(),
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'alpha_dash']);
    }

    public function test_announcement_keeps_its_own_slug_when_edited(): void
    {
        $announcement = $this->createAnnouncement('maintenance');

        Livewire::test(EditAnnouncement::class, ['record' => $announcement->getRouteKey()])
            ->fillForm(['description' => 'Updated'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Updated', $announcement->fresh()->description);
    }
}
