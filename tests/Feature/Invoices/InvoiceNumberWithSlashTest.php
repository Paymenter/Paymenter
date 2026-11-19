<?php

namespace Tests\Feature\Invoices;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceNumberWithSlashTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_page_opens_when_the_number_contains_a_slash(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => Invoice::STATUS_PENDING,
        ]);
        $invoice->forceFill(['number' => 'INV-2026/2'])->saveQuietly();

        $url = route('invoices.show', $invoice);
        $this->assertStringEndsWith('/invoices/INV-2026/2', $url);

        $this->withoutVite();

        $this->actingAs($user)
            ->withSession($this->loginUser($user))
            ->get($url)
            ->assertOk()
            ->assertSee('INV-2026/2');
    }

    public function test_file_name_replaces_slashes(): void
    {
        $invoice = new Invoice(['number' => 'FV/2026\\2']);

        $this->assertSame('FV-2026-2', $invoice->fileName());
    }
}
