<?php

namespace Tests\Feature\Admin;

use App\Admin\Resources\RoleResource\Pages\EditRole;
use App\Helpers\PermissionOptions;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_stored_permissions_that_are_no_longer_offered_are_listed()
    {
        $options = PermissionOptions::withStored(
            ['admin.users.view' => 'View Users'],
            ['admin.users.view', 'admin.removed.view', 123]
        );

        $this->assertSame([
            'admin.users.view' => 'View Users',
            'admin.removed.view' => __('admin.permissions.no_longer_available', ['permission' => 'admin.removed.view']),
        ], $options);

        $this->assertSame(['a' => 'A'], PermissionOptions::withStored(['a' => 'A'], null));
    }

    public function test_role_with_a_permission_no_longer_offered_can_be_saved()
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->create(['role_id' => 1]);

        $role = Role::create([
            'name' => 'Support',
            'permissions' => ['admin.users.view', 'admin.removed_extension.view'],
        ]);

        Livewire::actingAs($admin)
            ->test(EditRole::class, ['record' => $role->getRouteKey()])
            ->fillForm(['name' => 'Support staff'])
            ->call('save')
            ->assertHasNoFormErrors();

        $role->refresh();

        $this->assertSame('Support staff', $role->name);
        $this->assertEqualsCanonicalizing(['admin.users.view', 'admin.removed_extension.view'], $role->permissions);
    }
}
