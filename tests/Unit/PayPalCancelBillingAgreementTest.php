<?php

namespace Tests\Unit;

use App\Models\BillingAgreement;
use Illuminate\Support\Facades\Http;
use Paymenter\Extensions\Gateways\PayPal\PayPal;
use Tests\TestCase;

class PayPalCancelBillingAgreementTest extends TestCase
{
    private function cancel(int $status): bool
    {
        Http::fake([
            'api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'token']),
            'api-m.sandbox.paypal.com/v3/vault/payment-tokens/*' => Http::response(null, $status),
        ]);

        $gateway = new PayPal(['client_id' => 'id', 'client_secret' => 'secret', 'test_mode' => true]);

        return $gateway->cancelBillingAgreement(new BillingAgreement(['external_reference' => 'vault-token']));
    }

    public function test_revoked_token_is_reported_as_cancelled(): void
    {
        $this->assertTrue($this->cancel(204));

        Http::assertSent(fn ($request) => $request->method() === 'DELETE'
            && $request->url() === 'https://api-m.sandbox.paypal.com/v3/vault/payment-tokens/vault-token');
    }

    public function test_missing_token_is_reported_as_cancelled(): void
    {
        $this->assertTrue($this->cancel(404));
    }

    public function test_refused_request_is_reported_as_failed(): void
    {
        $this->assertFalse($this->cancel(403));
    }

    public function test_server_error_is_reported_as_failed(): void
    {
        $this->assertFalse($this->cancel(500));
    }
}
