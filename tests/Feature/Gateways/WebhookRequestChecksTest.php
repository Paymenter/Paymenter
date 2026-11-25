<?php

namespace Tests\Feature\Gateways;

use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Paymenter\Extensions\Gateways\Mollie\Mollie;
use Paymenter\Extensions\Gateways\PayPal\PayPal;
use Paymenter\Extensions\Gateways\PayPal_IPN\PayPal_IPN;
use Tests\TestCase;

class WebhookRequestChecksTest extends TestCase
{
    private function payPalHeaders(array $overrides = []): array
    {
        return array_merge([
            'HTTP_PAYPAL_AUTH_ALGO' => 'SHA256withRSA',
            'HTTP_PAYPAL_CERT_URL' => 'https://api.sandbox.paypal.com/v1/notifications/certs/CERT-360caa42-fca2a594-a5cafa77',
            'HTTP_PAYPAL_TRANSMISSION_ID' => '69cd13f0-d67a-11e5-baa3-778b53f4ae55',
            'HTTP_PAYPAL_TRANSMISSION_SIG' => 'signature',
            'HTTP_PAYPAL_TRANSMISSION_TIME' => '2016-02-18T20:01:35Z',
            'CONTENT_TYPE' => 'application/json',
        ], $overrides);
    }

    private function payPalWebhook(array $server)
    {
        $request = Request::create('/extensions/paypal/webhook', 'POST', server: $server, content: '{"event_type":"PAYMENT.CAPTURE.COMPLETED"}');

        return (new PayPal(['client_id' => 'id', 'client_secret' => 'secret', 'webhook_id' => 'wh', 'test_mode' => true]))->webhook($request);
    }

    public function test_mollie_webhook_refuses_an_id_that_is_not_a_payment_id()
    {
        Http::fake();
        $mollie = new Mollie(['api_key' => 'test_key']);

        foreach (['../customers', 'tr_abc?testmode=true', 'ord_abc', ''] as $id) {
            $this->assertSame(400, $mollie->webhook(Request::create('/', 'POST', ['id' => $id]))->getStatusCode());
        }

        $this->assertSame(400, $mollie->webhook(Request::create('/', 'POST', ['id' => ['tr_abc']]))->getStatusCode());

        Http::assertNothingSent();
    }

    public function test_mollie_webhook_looks_up_a_payment_id()
    {
        Http::fake(['api.mollie.com/*' => Http::response(['id' => 'tr_WDqYK6vllg', 'status' => 'open'])]);

        (new Mollie(['api_key' => 'test_key']))->webhook(Request::create('/', 'POST', ['id' => 'tr_WDqYK6vllg']));

        Http::assertSent(fn (ClientRequest $request) => $request->url() === 'https://api.mollie.com/v2/payments/tr_WDqYK6vllg');
    }

    public function test_paypal_webhook_refuses_requests_without_paypal_headers()
    {
        Http::fake();

        $this->assertSame(400, $this->payPalWebhook(['CONTENT_TYPE' => 'application/json'])->getStatusCode());
        $this->assertSame(400, $this->payPalWebhook($this->payPalHeaders(['HTTP_PAYPAL_TRANSMISSION_SIG' => '']))->getStatusCode());
        $this->assertSame(400, $this->payPalWebhook($this->payPalHeaders(['HTTP_PAYPAL_CERT_URL' => 'https://paypal.com.example.org/cert']))->getStatusCode());
        $this->assertSame(400, $this->payPalWebhook($this->payPalHeaders(['HTTP_PAYPAL_CERT_URL' => 'http://api.paypal.com/cert']))->getStatusCode());

        Http::assertNothingSent();
    }

    public function test_paypal_webhook_verifies_requests_with_paypal_headers()
    {
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'token']),
            '*/v1/notifications/verify-webhook-signature' => Http::response(['verification_status' => 'FAILURE']),
        ]);

        $this->assertSame(400, $this->payPalWebhook($this->payPalHeaders())->getStatusCode());

        Http::assertSent(fn (ClientRequest $request) => str_ends_with($request->url(), '/v1/notifications/verify-webhook-signature'));
    }

    public function test_paypal_ipn_ignores_notifications_for_another_receiver_without_posting_back()
    {
        Http::fake();
        $ipn = new PayPal_IPN(['email' => 'merchant@example.com', 'test_mode' => true]);

        $ipn->notify(Request::create('/', 'POST', ['receiver_email' => 'someone@example.com', 'payment_status' => 'Completed']));
        $ipn->notify(Request::create('/', 'POST', ['receiver_email' => 'merchant@example.com', 'payment_status' => 'Pending']));
        $ipn->notify(Request::create('/', 'POST'));

        Http::assertNothingSent();
    }

    public function test_paypal_ipn_posts_back_notifications_for_the_merchant()
    {
        Http::fake(['ipnpb.sandbox.paypal.com/*' => Http::response('INVALID')]);

        (new PayPal_IPN(['email' => 'merchant@example.com', 'test_mode' => true]))
            ->notify(Request::create('/', 'POST', ['receiver_email' => 'Merchant@Example.com', 'payment_status' => 'Completed']));

        Http::assertSentCount(1);
    }
}
