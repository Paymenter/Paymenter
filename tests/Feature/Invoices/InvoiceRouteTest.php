<?php

namespace Tests\Feature\Invoices;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_number_with_slashes_can_be_opened(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'number' => 'INV/2025/001',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->withSession($this->loginUser($user))
            ->get(route('invoices.show', $invoice))
            ->assertOk();
    }
}
