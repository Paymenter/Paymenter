<?php

namespace Tests\Feature\Invoices;

use App\Helpers\ExtensionHelper;
use App\Jobs\Server\CreateJob;
use App\Models\Gateway;
use App\Models\Invoice;
use App\Models\Server;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LatePaymentAfterCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_that_succeeds_after_the_order_was_cancelled_creates_the_service(): void
    {
        Queue::fake();
        config(['settings.cronjob_order_cancel' => 7]);

        $server = Server::create(['name' => 'Panel', 'extension' => 'Pterodactyl', 'type' => 'server', 'enabled' => true]);
        $product = $this->createProduct(['server_id' => $server->id]);
        $user = User::factory()->create();

        $service = Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'pending',
            'currency_code' => 'USD',
            'price' => 10.00,
            'created_at' => now()->subDays(8),
        ]);
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'due_at' => now()->subDays(1),
            'currency_code' => 'USD',
        ]);
        $invoice->items()->create([
            'reference_type' => Service::class,
            'reference_id' => $service->id,
            'price' => 10.00,
            'quantity' => 1,
            'description' => 'Server',
        ]);

        // The customer paid with a delayed method (e.g. SEPA Direct Debit); Stripe sent payment_intent.processing
        ExtensionHelper::addProcessingPayment($invoice->id, 'Stripe', 10.00, null, 'pi_test_late');

        $this->artisan('app:cron-job')->assertExitCode(0);
        $this->assertEquals('cancelled', $service->fresh()->status);
        $this->assertEquals('cancelled', $invoice->fresh()->status);

        // The payment settles after the cancellation
        $gateway = Gateway::create(['name' => 'Stripe', 'extension' => 'Stripe', 'type' => 'gateway', 'enabled' => true]);
        $gateway->settings()->create(['key' => 'stripe_webhook_secret', 'value' => 'whsec_test']);
        ExtensionHelper::call($gateway, 'boot');

        $body = json_encode([
            'id' => 'evt_test_late',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => [
                'id' => 'pi_test_late',
                'object' => 'payment_intent',
                'amount' => 1000,
                'currency' => 'usd',
                'status' => 'succeeded',
                'metadata' => ['invoice_id' => (string) $invoice->id],
            ]],
        ]);
        $timestamp = time();
        $signature = 't=' . $timestamp . ',v1=' . hash_hmac('sha256', $timestamp . '.' . $body, 'whsec_test');

        $this->call('POST', '/extensions/stripe/webhook', [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $body)->assertOk();

        $this->assertEquals('paid', $invoice->fresh()->status);
        $this->assertEquals('active', $service->fresh()->status);
        Queue::assertPushed(CreateJob::class, fn ($job) => $job->service->is($service));
    }
}
