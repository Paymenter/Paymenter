<?php

namespace Tests\Feature\Invoices;

use App\Enums\AdjustmentNoteStatus;
use App\Enums\AdjustmentNoteType;
use App\Livewire\Invoices\Show;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdjustmentNoteVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_voided_adjustment_notes_are_identified_on_the_client_invoice(): void
    {
        config(['settings.notes_client_visible' => true]);

        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => Invoice::STATUS_PENDING,
        ]);
        $invoice->items()->create([
            'description' => 'Test item',
            'quantity' => 1,
            'price' => 100,
        ]);
        $invoice->adjustmentNotes()->create([
            'type' => AdjustmentNoteType::Credit->value,
            'amount' => -20,
            'description' => 'Voided adjustment',
            'status' => AdjustmentNoteStatus::Voided,
        ]);

        Livewire::test(Show::class, ['invoice' => $invoice])
            ->assertSee(__('invoices.status'))
            ->assertSee('Voided')
            ->assertSee('Voided adjustment');

        $this->assertSame(100.0, $invoice->fresh()->total);
    }
}
