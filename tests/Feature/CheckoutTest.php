<?php

namespace Tests\Feature;

use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Once;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private $product = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = $this->createProduct();
    }

    public function test_product_visible_on_overview_page(): void
    {
        $response = $this->get(route('category.show', [
            $this->product->product->category->slug,
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->product->product->name);
    }

    public function test_product_visible_on_show_page(): void
    {
        $response = $this->get(route('products.show', [
            $this->product->product->category->slug,
            $this->product->product->slug,
        ]));
        $response->assertStatus(200);
        $response->assertSee($this->product->product->name);
    }

    public function test_checkout_page_redirects_to_cart_if_one_plan(): void
    {
        $response = $this->get(route('products.checkout', [
            $this->product->product->category->slug,
            $this->product->product->slug,
        ]));

        $response->assertRedirect(route('cart'));
        $cart = Cart::where('currency_code', 'USD')->first();
        $this->assertNotNull($cart);

        $this->assertNotNull($cart->items()->first());
        $response->assertCookie('cart', $cart->ulid);

        Once::flush();

        $response = $this->withCookie('cart', $cart->ulid)->get(route('cart'));
        $response->assertCookie('cart', $cart->ulid);
        $response->assertStatus(200);
        $response->assertSeeText($this->product->product->name);

        $this->assertDatabaseHas('carts', [
            'currency_code' => 'USD',
        ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $this->product->product->id,
            'plan_id' => $this->product->plan->id,
        ]);
    }

    public function test_checkout_page_with_multiple_plans(): void
    {
        // Add plan
        $plan = $this->product->product->plans()->create([
            'name' => 'Test Plan 2',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
        ]);
        $plan->prices()->create([
            'price' => 20.00,
            'currency_code' => 'USD',
        ]);

        $response = $this->get(route('products.checkout', [
            $this->product->product->category->slug,
            $this->product->product->slug,
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->product->product->name);
    }

    public function test_checkout_page_with_changed_plan(): void
    {
        // Add plan
        $plan = $this->product->product->plans()->create([
            'name' => 'Test Plan 2',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
        ]);
        $plan->prices()->create([
            'price' => 20.00,
            'currency_code' => 'USD',
        ]);

        // Change plan
        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->assertSee($this->product->product->name)
            ->assertSee('$10.00')
            ->set('plan_id', $plan->id)
            ->call('updatePricing')
            ->assertSee($this->product->plan->name)
            ->assertSee('$20.00')
            ->call('checkout');

        $this->assertDatabaseHas('carts', [
            'currency_code' => 'USD',
        ]);
    }

    public function test_checkout_page_with_plan_not_in_product(): void
    {
        $plan = $this->product->product->plans()->create([
            'name' => 'Test Plan 2',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
        ]);
        $plan->prices()->create([
            'price' => 20.00,
            'currency_code' => 'USD',
        ]);

        // Add plan
        $plan = $this->createProduct()->plan;

        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->assertSee($this->product->product->name)
            ->assertSee('$10.00')
            ->set('plan_id', $plan->id)
            ->call('checkout')
            ->assertHasErrors(['plan_id' => 'exists']);
    }
}
