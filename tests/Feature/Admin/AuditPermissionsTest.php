<?php

namespace Tests\Feature\Admin;

use App\Admin\Resources\Audits\Pages\ListAudits;
use App\Models\Audit;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuditPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private function adminWith(array $permissions): User
    {
        $role = Role::create(['name' => 'Auditor', 'permissions' => $permissions]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_view_audits_permission_opens_the_audit_log(): void
    {
        Livewire::actingAs($this->adminWith(['admin.audits.viewAny']))
            ->test(ListAudits::class)
            ->assertOk();
    }

    public function test_audit_log_is_closed_without_the_permission(): void
    {
        Livewire::actingAs($this->adminWith(['admin.users.viewAny']))
            ->test(ListAudits::class)
            ->assertForbidden();
    }

    public function test_view_audits_permission_does_not_allow_deleting(): void
    {
        $user = $this->adminWith(['admin.audits.viewAny']);

        Livewire::actingAs($user)
            ->test(ListAudits::class)
            ->assertTableBulkActionHidden('delete');

        $this->assertFalse($user->can('deleteAny', Audit::class));
        $this->assertFalse($user->can('create', Audit::class));
    }

    public function test_delete_permission_allows_bulk_deleting(): void
    {
        Livewire::actingAs($this->adminWith(['admin.audits.viewAny', 'admin.audits.deleteAny']))
            ->test(ListAudits::class)
            ->assertTableBulkActionVisible('delete');
    }
}
