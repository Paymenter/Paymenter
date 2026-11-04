<?php

namespace Tests\Feature;

use App\Helpers\ExtensionHelper;
use App\Models\Gateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CheckoutGatewaysTest extends TestCase
{
    use RefreshDatabase;

    private Gateway $stripe;

    private Gateway $paypal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stripe = Gateway::create(['name' => 'Stripe', 'extension' => 'Stripe', 'type' => 'gateway', 'enabled' => true]);
        $this->paypal = Gateway::create(['name' => 'PayPal', 'extension' => 'PayPal', 'type' => 'gateway', 'enabled' => true]);
    }

    public function test_all_gateways_are_offered_without_listeners(): void
    {
        $gateways = ExtensionHelper::getCheckoutGateways(10, 'USD', 'cart');

        $this->assertEqualsCanonicalizing(
            [$this->stripe->id, $this->paypal->id],
            array_map(fn ($gateway) => $gateway->id, $gateways)
        );
    }

    public function test_listener_can_exclude_a_gateway(): void
    {
        $received = null;

        Event::listen('checkout.gateways', function ($gateways, $total, $currency, $type, $items) use (&$received) {
            $received = [count($gateways), $total, $currency, $type];

            return $total < 50 ? [$this->paypal->id] : [];
        });

        $gateways = ExtensionHelper::getCheckoutGateways(10, 'USD', 'cart');

        $this->assertSame([2, 10, 'USD', 'cart'], $received);
        $this->assertSame([$this->stripe->id], array_map(fn ($gateway) => $gateway->id, $gateways));

        $gateways = ExtensionHelper::getCheckoutGateways(100, 'USD', 'cart');

        $this->assertCount(2, $gateways);
    }

    public function test_listener_returning_nothing_changes_nothing(): void
    {
        Event::listen('checkout.gateways', fn () => null);

        $this->assertCount(2, ExtensionHelper::getCheckoutGateways(10, 'USD', 'invoice'));
    }
}
