<?php

namespace Tests\Feature\Gateways;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Paymenter\Extensions\Gateways\Stripe\Stripe;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_test_secret';

    private function invoice(): Invoice
    {
        $invoice = Invoice::factory()->create([
            'user_id' => User::factory()->create()->id,
            'status' => Invoice::STATUS_PENDING,
        ]);

        $invoice->items()->create([
            'description' => 'Test Item',
            'quantity' => 1,
            'price' => 10.00,
        ]);

        return $invoice->fresh();
    }

    private function deliver(Invoice $invoice, int $timestamp)
    {
        $payload = json_encode([
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => [
                'id' => 'pi_test_123',
                'amount' => 1000,
                'metadata' => ['invoice_id' => $invoice->id],
            ]],
        ]);

        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, self::SECRET);

        $request = Request::create('/extensions/stripe/webhook', 'POST', server: [
            'HTTP_STRIPE_SIGNATURE' => 't=' . $timestamp . ',v1=' . $signature,
            'CONTENT_TYPE' => 'application/json',
        ], content: $payload);

        return (new Stripe(['stripe_webhook_secret' => self::SECRET]))->webhook($request);
    }

    public function test_freshly_signed_event_is_accepted()
    {
        $invoice = $this->invoice();

        $this->deliver($invoice, time());

        $this->assertDatabaseHas('invoice_transactions', [
            'invoice_id' => $invoice->id,
            'transaction_id' => 'pi_test_123',
        ]);
    }

    public function test_event_signed_more_than_five_minutes_ago_is_refused()
    {
        $invoice = $this->invoice();

        $response = $this->deliver($invoice, time() - 301);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertDatabaseMissing('invoice_transactions', ['invoice_id' => $invoice->id]);
    }
}
