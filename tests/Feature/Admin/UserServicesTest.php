<?php

namespace Tests\Feature\Admin;

use App\Admin\Resources\UserResource\Pages\ShowServices;
use App\Models\Category;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserServicesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Service $domain;

    private Service $hosting;

    private Service $vps;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
        $this->actingAs(User::factory()->create(['role_id' => Role::where('name', 'admin')->value('id')]));

        $this->user = User::factory()->create();

        $this->domain = $this->createService('Domains', 'example.com registration', Service::STATUS_ACTIVE);
        $this->hosting = $this->createService('Hosting', 'Starter Hosting', Service::STATUS_SUSPENDED);
        $this->vps = $this->createService('Servers', 'Small VPS', Service::STATUS_CANCELLED, User::factory()->create());
    }

    private function createService(string $category, string $product, string $status, ?User $user = null): Service
    {
        $created = $this->createProduct(['category_id' => Category::factory()->create(['name' => $category])->id]);
        $created->product->update(['name' => $product]);

        return Service::factory()->create([
            'user_id' => ($user ?? $this->user)->id,
            'product_id' => $created->product->id,
            'plan_id' => $created->plan->id,
            'status' => $status,
        ]);
    }

    private function page()
    {
        return Livewire::test(ShowServices::class, ['record' => $this->user->getRouteKey()]);
    }

    public function test_lists_only_the_users_services()
    {
        $this->page()
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$this->domain, $this->hosting])
            ->assertCanNotSeeTableRecords([$this->vps]);
    }

    public function test_category_filter_only_offers_the_users_categories()
    {
        $options = $this->page()->instance()->getTable()->getFilter('category')->getOptions();

        $this->assertEqualsCanonicalizing(['Domains', 'Hosting'], array_values($options));
    }

    public function test_filters_by_category()
    {
        $this->page()
            ->filterTable('category', $this->hosting->product->category_id)
            ->assertCanSeeTableRecords([$this->hosting])
            ->assertCanNotSeeTableRecords([$this->domain]);
    }

    public function test_filters_by_status()
    {
        $this->page()
            ->filterTable('status', Service::STATUS_ACTIVE)
            ->assertCanSeeTableRecords([$this->domain])
            ->assertCanNotSeeTableRecords([$this->hosting]);
    }

    public function test_searches_by_product_name_and_id()
    {
        $this->page()
            ->searchTable('Starter')
            ->assertCanSeeTableRecords([$this->hosting])
            ->assertCanNotSeeTableRecords([$this->domain]);

        $this->page()
            ->searchTable((string) $this->domain->id)
            ->assertCanSeeTableRecords([$this->domain]);
    }

    public function test_search_does_not_leak_other_users_services()
    {
        $this->page()
            ->searchTable('Small VPS')
            ->assertCanNotSeeTableRecords([$this->vps]);
    }

    public function test_sorts_by_status()
    {
        $this->page()
            ->sortTable('status')
            ->assertCanSeeTableRecords([$this->domain, $this->hosting], inOrder: true)
            ->sortTable('status', 'desc')
            ->assertCanSeeTableRecords([$this->hosting, $this->domain], inOrder: true);
    }
}
