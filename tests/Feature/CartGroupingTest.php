<?php

namespace Tests\Feature;

use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Once;
use Tests\TestCase;

class CartGroupingTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_cart_item_keeps_the_original_card(): void
    {
        $product = $this->product('Standalone');
        $cart = Cart::create(['currency_code' => 'USD']);

        $cart->items()->create([
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'config_options' => [],
            'checkout_config' => [],
            'quantity' => 1,
        ]);

        $response = $this->renderCart($cart);

        $response->assertStatus(200);
        $response->assertSeeText('Standalone');
        $response->assertSeeText('$10.00');
        $response->assertDontSee('data-cart-group', false);
    }

    public function test_cart_groups_child_items_into_one_card_with_item_prices_and_total(): void
    {
        $hosting = $this->product('Hosting');
        $domain = $this->product('Domain', 20.00);

        $cart = Cart::create(['currency_code' => 'USD']);

        $hostingItem = $cart->items()->create([
            'product_id' => $hosting->product->id,
            'plan_id' => $hosting->plan->id,
            'config_options' => [],
            'checkout_config' => [],
            'quantity' => 1,
        ]);

        $cart->items()->create([
            'product_id' => $domain->product->id,
            'plan_id' => $domain->plan->id,
            'config_options' => [],
            'checkout_config' => ['domain' => 'example.com', 'years' => 1],
            'quantity' => 1,
            'parent_id' => $hostingItem->id,
        ]);

        $response = $this->renderCart($cart);

        $response->assertStatus(200);
        $response->assertSeeText('Hosting');
        $response->assertSeeText('Domain');
        $response->assertSeeText('$10.00');
        $response->assertSeeText('$20.00');
        $response->assertSeeText('$30.00');

        $this->assertSame(1, substr_count($response->getContent(), 'data-cart-group'));
    }

    public function test_standalone_items_stay_separate_from_grouped_items(): void
    {
        $standalone = $this->product('Standalone', 5.00);
        $hosting = $this->product('Hosting');
        $domain = $this->product('Domain', 20.00);

        $cart = Cart::create(['currency_code' => 'USD']);

        $cart->items()->create([
            'product_id' => $standalone->product->id,
            'plan_id' => $standalone->plan->id,
            'config_options' => [],
            'checkout_config' => [],
            'quantity' => 1,
        ]);

        $hostingItem = $cart->items()->create([
            'product_id' => $hosting->product->id,
            'plan_id' => $hosting->plan->id,
            'config_options' => [],
            'checkout_config' => [],
            'quantity' => 1,
        ]);

        $cart->items()->create([
            'product_id' => $domain->product->id,
            'plan_id' => $domain->plan->id,
            'config_options' => [],
            'checkout_config' => ['domain' => 'example.com', 'years' => 1],
            'quantity' => 1,
            'parent_id' => $hostingItem->id,
        ]);

        $response = $this->renderCart($cart);

        $response->assertStatus(200);
        $response->assertSeeText('Standalone');
        $response->assertSeeText('Hosting');
        $response->assertSeeText('Domain');
        $response->assertSeeText('$5.00');
        $response->assertSeeText('$30.00');
        $response->assertSeeText('$35.00');

        $this->assertSame(1, substr_count($response->getContent(), 'data-cart-group'));
    }

    private function product(string $name, float $price = 10.00)
    {
        $product = $this->createProduct();
        $product->product->update(['name' => $name]);
        $product->plan->prices()->update(['price' => $price]);

        return $product;
    }

    private function renderCart(Cart $cart)
    {
        Once::flush();

        return $this->withCookie('cart', $cart->ulid)->get(route('cart'));
    }
}
