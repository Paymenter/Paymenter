<?php

namespace Tests\Feature;

use App\Classes\Cart;
use App\Exceptions\DisplayException;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponExpiryTest extends TestCase
{
    use RefreshDatabase;

    private function coupon(string $expiresAt): Coupon
    {
        return Coupon::create([
            'type' => 'percentage',
            'code' => 'SAVE10',
            'value' => 10,
            'expires_at' => $expiresAt,
        ]);
    }

    public function test_coupon_is_valid_on_its_expiry_date(): void
    {
        $this->travelTo(now()->setTime(15, 0));
        $this->coupon(now()->toDateString());

        $this->assertSame('SAVE10', Cart::validateCoupon('SAVE10')->code);
    }

    public function test_coupon_is_expired_the_day_after(): void
    {
        $this->travelTo(now()->setTime(0, 5));
        $this->coupon(now()->subDay()->toDateString());

        $this->expectException(DisplayException::class);
        Cart::validateCoupon('SAVE10');
    }
}
