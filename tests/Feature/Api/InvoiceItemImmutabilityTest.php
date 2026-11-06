<?php

namespace Tests\Feature\Api;

use App\Models\ApiKey;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Setting;
use App\Models\User;
use App\Providers\SettingsProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class InvoiceItemImmutabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_items_cannot_be_created_updated_or_deleted_for_immutable_published_invoices(): void
    {
        $this->enableImmutableInvoices();
        $token = $this->createApiKey([
            'admin.invoice_items.create',
            'admin.invoice_items.update',
            'admin.invoice_items.delete',
        ]);
        $invoice = $this->createInvoice(Invoice::STATUS_PENDING);
        $item = $this->createInvoiceItem($invoice);

        $this->withToken($token)->postJson('/api/v1/admin/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'New item',
            'price' => 20,
            'quantity' => 1,
        ])->assertForbidden();

        $this->withToken($token)->patchJson('/api/v1/admin/invoice-items/' . $item->id, [
            'price' => 20,
        ])->assertForbidden();

        $this->withToken($token)->deleteJson('/api/v1/admin/invoice-items/' . $item->id)
            ->assertForbidden();
    }

    public function test_invoice_items_remain_mutable_for_draft_invoices(): void
    {
        $this->enableImmutableInvoices();
        $token = $this->createApiKey([
            'admin.invoice_items.create',
            'admin.invoice_items.update',
            'admin.invoice_items.delete',
        ]);
        $invoice = $this->createInvoice(Invoice::STATUS_DRAFT);
        $item = $this->createInvoiceItem($invoice);

        $this->withToken($token)->postJson('/api/v1/admin/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'New item',
            'price' => 20,
            'quantity' => 1,
        ])->assertSuccessful();

        $this->withToken($token)->patchJson('/api/v1/admin/invoice-items/' . $item->id, [
            'price' => 20,
        ])->assertSuccessful();

        $this->withToken($token)->deleteJson('/api/v1/admin/invoice-items/' . $item->id)
            ->assertNoContent();
    }

    private function createApiKey(array $permissions): string
    {
        $token = 'invoice-item-test-token';

        ApiKey::create([
            'name' => 'Invoice item test key',
            'permissions' => $permissions,
            'token' => hash('sha256', $token),
            'type' => 'admin',
            'enabled' => true,
        ]);

        return $token;
    }

    private function enableImmutableInvoices(): void
    {
        Setting::updateOrCreate(
            ['key' => 'immutable_invoices_enabled', 'settingable_type' => null],
            ['value' => true]
        );
        Cache::forget('settings');
        SettingsProvider::getSettings(true);
    }

    private function createInvoice(string $status): Invoice
    {
        return Invoice::factory()->create([
            'user_id' => User::factory()->create()->id,
            'status' => $status,
            'currency_code' => 'USD',
        ]);
    }

    private function createInvoiceItem(Invoice $invoice): InvoiceItem
    {
        return $invoice->items()->create([
            'description' => 'Existing item',
            'price' => 10,
            'quantity' => 1,
        ]);
    }
}
