<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCouponPriceTest extends TestCase
{
    use RefreshDatabase;

    private function serviceWithCoupon(array $coupon, int $paidInvoices = 0): Service
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $coupon = Coupon::create(array_merge([
            'code' => 'SAVE50',
            'type' => 'percentage',
            'applies_to' => 'all',
            'value' => 50,
        ], $coupon));

        $service = Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'active',
            'currency_code' => 'USD',
            'price' => 10.00,
            'coupon_id' => $coupon->id,
        ]);

        for ($i = 0; $i < $paidInvoices; $i++) {
            $invoice = Invoice::factory()->create([
                'user_id' => $user->id,
                'status' => 'paid',
                'currency_code' => 'USD',
            ]);
            $invoice->items()->create([
                'reference_id' => $service->id,
                'reference_type' => Service::class,
                'description' => 'Service',
                'quantity' => 1,
                'price' => 5.00,
            ]);
        }

        return $service->fresh();
    }

    public function test_coupon_without_recurring_only_discounts_the_first_invoice(): void
    {
        $this->assertEquals('5.00', $this->serviceWithCoupon(['recurring' => null])->calculatePrice());
        $this->assertEquals('10.00', $this->serviceWithCoupon(['recurring' => null], paidInvoices: 1)->calculatePrice());
    }

    public function test_coupon_with_zero_recurring_discounts_every_invoice(): void
    {
        $this->assertEquals('5.00', $this->serviceWithCoupon(['recurring' => 0], paidInvoices: 3)->calculatePrice());
    }

    public function test_coupon_with_recurring_discounts_that_many_invoices(): void
    {
        $this->assertEquals('5.00', $this->serviceWithCoupon(['recurring' => 2], paidInvoices: 1)->calculatePrice());
        $this->assertEquals('10.00', $this->serviceWithCoupon(['recurring' => 2], paidInvoices: 2)->calculatePrice());
    }

    public function test_coupon_limited_to_another_product_does_not_discount(): void
    {
        $service = $this->serviceWithCoupon(['recurring' => 0]);
        $other = $this->createProduct();
        $service->coupon->products()->attach($other->product->id);

        $this->assertEquals('10.00', $service->fresh()->calculatePrice());

        $service->coupon->products()->attach($service->product_id);

        $this->assertEquals('5.00', $service->fresh()->calculatePrice());
    }
}
