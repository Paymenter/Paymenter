<?php

namespace Tests\Feature;

use App\Classes\Cart as CartHelper;
use App\Exceptions\DisplayException;
use App\Livewire\Cart as CartComponent;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Once;
use Livewire\Livewire;
use Tests\TestCase;

class CouponValidateEventTest extends TestCase
{
    use RefreshDatabase;

    private Cart $cart;

    private Coupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();

        $product = $this->createProduct();

        $this->coupon = Coupon::create([
            'code' => 'HALF',
            'type' => 'percentage',
            'applies_to' => 'all',
            'value' => 50,
        ]);

        $this->cart = Cart::create(['currency_code' => 'USD', 'coupon_id' => $this->coupon->id]);
        $this->cart->items()->create([
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'config_options' => [],
            'checkout_config' => [],
            'quantity' => 2,
        ]);

        $this->app['request']->cookies->set('cart', $this->cart->ulid);
        Once::flush();
    }

    public function test_coupon_is_valid_without_listeners(): void
    {
        $this->assertTrue(CartHelper::validateCoupon('HALF')->is($this->coupon));
    }

    public function test_listener_receives_coupon_user_and_pre_discount_amount(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $received = null;
        Event::listen('coupon.validate', function ($coupon, $user, $context) use (&$received) {
            $received = [$coupon->id, $user?->id, $context];

            return null;
        });

        CartHelper::validateCoupon('HALF');

        $this->assertSame([$this->coupon->id, $user->id, ['amount' => 20.0, 'currency' => 'USD']], $received);
    }

    public function test_listener_can_refuse_a_coupon(): void
    {
        Event::listen('coupon.validate', fn () => '');
        Event::listen('coupon.validate', fn ($coupon, $user) => $user ? null : 'Sign in to use this code');

        $this->expectException(DisplayException::class);
        $this->expectExceptionMessage('Sign in to use this code');

        CartHelper::validateCoupon('HALF');
    }

    public function test_refused_coupon_blocks_checkout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Event::listen('coupon.validate', fn () => 'New customers only');

        Livewire::withCookie('cart', $this->cart->ulid)
            ->test(CartComponent::class)
            ->call('checkout')
            ->assertDispatched('notify', fn ($name, $params) => $params[0]['message'] === 'This coupon can no longer be used')
            ->assertSet('coupon', null);

        $this->assertSame(0, Order::count());
    }
}
