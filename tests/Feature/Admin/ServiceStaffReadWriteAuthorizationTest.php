<?php

namespace Tests\Feature\Admin;

use App\Admin\Resources\Common\RelationManagers\PropertiesRelationManager;
use App\Admin\Resources\ServiceResource\Pages\EditService;
use App\Models\Currency;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Support\ServiceAdminAuthorization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceStaffReadWriteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = false;

    private User $customer;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Currency::firstOrCreate(['code' => 'USD'], [
            'name' => 'US Dollar',
            'prefix' => '$',
            'suffix' => '',
            'format' => '1,000.00',
        ]);

        $product = $this->createProduct();
        $this->customer = User::factory()->create();
        $this->service = Service::factory()->create([
            'user_id' => $this->customer->id,
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'status' => Service::STATUS_ACTIVE,
            'currency_code' => 'USD',
            'price' => 10.00,
            'quantity' => 1,
            'expires_at' => now()->addMonth(),
        ]);
    }

    public function test_view_any_staff_can_view_but_not_update_on_admin(): void
    {
        $staff = $this->staff(['admin.services.viewAny']);
        $this->actingAsStaffOnAdmin($staff);

        $this->assertTrue($staff->can('view', $this->service));
        $this->assertFalse($staff->can('update', $this->service));
        $this->assertFalse(ServiceAdminAuthorization::canUpdate($staff, $this->service));
    }

    public function test_view_any_does_not_grant_storefront_view_of_other_users_services(): void
    {
        $staff = $this->staff(['admin.services.viewAny']);
        $this->actingAs($staff);

        $this->assertFalse($staff->can('view', $this->service));
        $this->assertFalse($staff->can('update', $this->service));
        $this->assertTrue($this->customer->can('view', $this->service));
        $this->assertTrue($this->customer->can('update', $this->service));
    }

    public function test_view_any_staff_can_open_edit_service_but_cannot_save_or_trigger_extension(): void
    {
        $staff = $this->staff(['admin.services.viewAny']);

        $component = Livewire::actingAs($staff)
            ->test(EditService::class, ['record' => $this->service->getRouteKey()]);

        $component->assertSuccessful();
        $component->assertActionHidden('changeStatus');
        $component->assertActionHidden('delete');

        Livewire::actingAs($staff)
            ->test(EditService::class, ['record' => $this->service->getRouteKey()])
            ->call('save')
            ->assertForbidden();

        $this->assertSame(10.00, (float) $this->service->fresh()->price);
        $this->assertSame(Service::STATUS_ACTIVE, $this->service->fresh()->status);
    }

    public function test_update_staff_can_save_service(): void
    {
        $staff = $this->staff(['admin.services.viewAny', 'admin.services.update']);

        Livewire::actingAs($staff)
            ->test(EditService::class, ['record' => $this->service->getRouteKey()])
            ->fillForm([
                'price' => 25.00,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(25.00, (float) $this->service->fresh()->price);
    }

    public function test_view_any_staff_cannot_inline_edit_property_value(): void
    {
        $staff = $this->staff(['admin.services.viewAny']);
        $property = $this->service->properties()->create(['key' => 'k1', 'name' => 'k1', 'value' => 'v0']);

        Livewire::actingAs($staff)
            ->test(PropertiesRelationManager::class, [
                'ownerRecord' => $this->service,
                'pageClass' => EditService::class,
            ])
            ->assertSuccessful()
            ->call('updateTableColumnState', 'value', (string) $property->getKey(), 'v1');

        $this->assertSame('v0', $property->fresh()->value);
    }

    public function test_update_staff_can_inline_edit_property_value(): void
    {
        $staff = $this->staff(['admin.services.viewAny', 'admin.services.update']);
        $property = $this->service->properties()->create(['key' => 'k1', 'name' => 'k1', 'value' => 'v0']);

        Livewire::actingAs($staff)
            ->test(PropertiesRelationManager::class, [
                'ownerRecord' => $this->service,
                'pageClass' => EditService::class,
            ])
            ->assertSuccessful()
            ->call('updateTableColumnState', 'value', (string) $property->getKey(), 'v1');

        $this->assertSame('v1', $property->fresh()->value);
    }

    private function staff(array $permissions): User
    {
        $role = Role::create([
            'name' => 'Staff ' . uniqid('', true),
            'permissions' => $permissions,
        ]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function actingAsStaffOnAdmin(User $user): void
    {
        $this->actingAs($user);
        $request = Request::create('/admin/services/services/' . $this->service->id . '/edit', 'GET');
        $request->setUserResolver(fn () => $user);
        $this->app->instance('request', $request);
    }
}
