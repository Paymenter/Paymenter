<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCurrencyTest extends TestCase
{
    use RefreshDatabase;

    private $product = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = $this->createProduct();
    }

    private function makeCartWithItem(string $currency): Cart
    {
        $cart = Cart::create(['currency_code' => $currency]);
        $cart->items()->create([
            'product_id' => $this->product->product->id,
            'plan_id' => $this->product->plan->id,
            'config_options' => [],
            'checkout_config' => [],
            'quantity' => 1,
        ]);

        return $cart->fresh();
    }

    public function test_item_price_follows_cart_currency_when_session_diverges(): void
    {
        Currency::create(['code' => 'EUR', 'name' => 'Euro', 'suffix' => 'EUR', 'format' => '1.000,00']);
        $this->product->plan->prices()->create([
            'price' => 100.00,
            'currency_code' => 'EUR',
        ]);

        // Cart stamped USD, then the session diverges (e.g. expired back to the default currency).
        $cart = $this->makeCartWithItem('USD');
        session(['currency' => 'EUR']);

        $price = $cart->items->first()->price;

        $this->assertEquals('USD', $price->currency->code);
        $this->assertEquals(10.00, (float) $price->price);
    }

    public function test_item_without_price_row_in_cart_currency_is_unavailable(): void
    {
        Currency::create(['code' => 'EUR', 'name' => 'Euro', 'suffix' => 'EUR', 'format' => '1.000,00']);

        // Cart locked to EUR, but the plan is only priced in USD: must be unavailable, not free.
        $cart = $this->makeCartWithItem('EUR');

        session(['currency' => 'USD']);

        $price = $cart->items->first()->price;

        $this->assertFalse($price->available);
        $this->assertEquals(0.0, (float) $price->price);
    }
}
