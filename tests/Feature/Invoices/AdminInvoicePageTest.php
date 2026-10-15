<?php

namespace Tests\Feature\Invoices;

use App\Admin\Resources\InvoiceResource;
use App\Admin\Resources\InvoiceResource\Pages\EditInvoice;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminInvoicePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_keeps_resolving_an_invoice_whose_number_differs_from_its_id(): void
    {
        Setting::updateOrCreate(['key' => 'invoice_number'], ['value' => 1000]);
        Setting::updateOrCreate(['key' => 'invoice_number_format'], ['value' => '{number}']);

        $admin = User::factory()->create(['role_id' => 1]);
        $invoice = Invoice::factory()->create([
            'user_id' => $admin->id,
            'status' => Invoice::STATUS_PENDING,
        ]);
        $this->assertNotEquals((string) $invoice->id, (string) $invoice->number);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->assertStringEndsWith('/invoices/' . $invoice->id . '/edit', InvoiceResource::getUrl('edit', ['record' => $invoice]));

        Livewire::actingAs($admin)
            ->test(EditInvoice::class, ['record' => $invoice->id])
            ->assertOk()
            // Every Livewire request after the first resolves the record again
            ->call('$refresh')
            ->assertOk()
            ->assertSet('record.id', $invoice->id);
    }
}
