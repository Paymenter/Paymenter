<?php

namespace Tests\Feature;

use App\Helpers\ExtensionHelper;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Paymenter\Extensions\Gateways\Stripe\Stripe;
use Tests\TestCase;

class StripeZeroDecimalCurrencyTest extends TestCase
{
    use RefreshDatabase;

    private function invoice(string $currency): Invoice
    {
        Currency::firstOrCreate(['code' => $currency], ['name' => $currency, 'prefix' => '', 'suffix' => '']);
        $invoice = Invoice::factory()->create([
            'user_id' => User::factory()->create()->id,
            'status' => Invoice::STATUS_PENDING,
            'currency_code' => $currency,
        ]);
        $invoice->items()->create(['description' => 'Item', 'quantity' => 1, 'price' => 80]);

        return $invoice->fresh();
    }

    private function sentAmount(string $path): int
    {
        $amount = null;
        Http::assertSent(function (ClientRequest $request) use ($path, &$amount) {
            if (str_ends_with($request->url(), $path)) {
                $amount = (int) $request['amount'];
            }

            return true;
        });

        return $amount;
    }

    public function test_pay_sends_the_amount_in_the_unit_stripe_expects()
    {
        View::addNamespace('gateways.stripe', base_path('extensions/Gateways/Stripe/resources/views'));
        Http::fake(['*' => Http::response(['id' => 'x', 'data' => []])]);

        (new Stripe(['stripe_secret_key' => 'sk']))->pay($this->invoice('JPY'), 80);
        $this->assertSame(80, $this->sentAmount('/payment_intents'));

        Http::fake(['*' => Http::response(['id' => 'x', 'data' => []])]);
        (new Stripe(['stripe_secret_key' => 'sk']))->pay($this->invoice('USD'), 19.99);
        $this->assertSame(1999, $this->sentAmount('/payment_intents'));
    }

    public function test_refund_sends_the_amount_in_the_unit_stripe_expects()
    {
        Http::fake(['*' => Http::response(['id' => 're'])]);
        $invoice = $this->invoice('JPY');
        ExtensionHelper::addPayment($invoice->id, 'Stripe', 80, null, 'pi_1');

        (new Stripe(['stripe_secret_key' => 'sk']))->refund(InvoiceTransaction::where('transaction_id', 'pi_1')->first(), 80);

        $this->assertSame(80, $this->sentAmount('/refunds'));
    }

    public function test_webhook_records_the_amount_stripe_reports()
    {
        $invoice = $this->invoice('JPY');
        $payload = json_encode([
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => [
                'id' => 'pi_2',
                'amount' => 80,
                'currency' => 'jpy',
                'metadata' => ['invoice_id' => $invoice->id],
            ]],
        ]);
        $time = time();
        $signature = 't=' . $time . ',v1=' . hash_hmac('sha256', $time . '.' . $payload, 'whsec');
        $request = Request::create('/', 'POST', [], [], [], ['HTTP_STRIPE_SIGNATURE' => $signature], $payload);

        (new Stripe(['stripe_webhook_secret' => 'whsec']))->webhook($request);

        $this->assertEquals(80, InvoiceTransaction::where('transaction_id', 'pi_2')->first()->amount);
    }
}
