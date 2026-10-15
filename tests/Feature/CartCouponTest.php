<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Once;
use Livewire\Livewire;
use Tests\TestCase;

class CartCouponTest extends TestCase
{
    use RefreshDatabase;

    private $product = null;

    private Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = $this->createProduct();

        $this->cart = Cart::create(['currency_code' => 'USD']);
        $this->cart->items()->create([
            'product_id' => $this->product->product->id,
            'plan_id' => $this->product->plan->id,
            'config_options' => [],
            'checkout_config' => [],
            'quantity' => 1,
        ]);
    }

    private function coupon(string $code): Coupon
    {
        return Coupon::create([
            'code' => $code,
            'type' => 'percentage',
            'applies_to' => 'all',
            'value' => 50,
        ]);
    }

    public function test_applying_a_coupon_shows_the_one_already_applied_in_another_tab(): void
    {
        $applied = $this->coupon('FIRST');
        $this->coupon('SECOND');

        $component = Livewire::withCookie('cart', $this->cart->ulid)->test('cart');

        // Another tab applies a coupon after this one rendered
        $this->cart->update(['coupon_id' => $applied->id]);
        Once::flush();

        // The typed code and the action arrive in one request, as with a deferred wire:model
        $component->update(
            calls: [['method' => 'applyCoupon', 'params' => [], 'path' => '']],
            updates: ['coupon' => 'SECOND'],
        )
            ->assertOk()
            ->assertSee('FIRST');

        $this->assertTrue($component->get('coupon')->is($applied));
    }

    public function test_checkout_uses_the_current_cart_total(): void
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)->withCookie('cart', $this->cart->ulid)->test('cart');

        // The cart becomes free after the page rendered
        $this->product->plan->prices()->update(['price' => 0]);
        Once::flush();

        $component->call('checkout');

        $this->assertSame(0, Invoice::count());
        $this->assertSame('active', $user->services()->first()->status);
    }
}
