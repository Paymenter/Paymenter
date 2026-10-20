<?php

namespace Tests\Feature;

use App\Events\Invoice\Finalized;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Paymenter\Extensions\Others\DiscordNotifications\DiscordNotifications;
use Tests\TestCase;

class DiscordNotificationsDeletedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_created_for_a_deleted_user_is_still_sent(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id, 'status' => Invoice::STATUS_DRAFT]);
        $user->delete();

        Http::fake();
        (new DiscordNotifications([
            'webhook_url' => 'https://discord.test/api/webhooks/1/token',
            'ping_type' => 'none',
            'events' => ['Invoice Created'],
        ]))->boot();

        event(new Finalized($invoice->fresh(), false));

        Http::assertSent(function (Request $request) {
            $fields = array_column($request->data()['embeds'][0]['fields'], 'value', 'name');

            return $fields['User'] === __('invoices.deleted_user');
        });
    }
}
