<?php

namespace Tests\Unit;

use App\Models\Price;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceUpgradeQuantityTest extends TestCase
{
    use RefreshDatabase;

    public function test_upgrade_price_covers_every_unit_of_the_service(): void
    {
        // $10 -> $20 per unit, a full period left, 3 units: 3 x $10
        $upgrade = $this->makeUpgrade(newPrice: 20.00, quantity: 3);

        $this->assertEquals(30.00, $upgrade->calculatePrice()->price);
    }

    public function test_downgrade_credit_covers_every_unit_of_the_service(): void
    {
        // $10 -> $5 per unit, a full period left, 3 units: 3 x -$5
        $upgrade = $this->makeUpgrade(newPrice: 5.00, quantity: 3);

        $this->assertEquals(-15.00, $upgrade->calculatePrice()->price);
    }

    public function test_upgrade_price_of_a_single_unit_service_is_unchanged(): void
    {
        $upgrade = $this->makeUpgrade(newPrice: 20.00, quantity: 1);

        $this->assertEquals(10.00, $upgrade->calculatePrice()->price);
    }

    private function makeUpgrade(float $newPrice, int $quantity): ServiceUpgrade
    {
        $user = User::factory()->create();
        $old = $this->createProduct();
        $new = $this->createProduct();
        Price::where('plan_id', $new->plan->id)->update(['price' => $newPrice]);

        $service = Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $old->plan->id,
            'product_id' => $old->product->id,
            'status' => 'active',
            'price' => 10.00,
            'quantity' => $quantity,
            // A monthly plan counts as 30 days, so the whole period is left
            'expires_at' => now()->addDays(30),
            'currency_code' => 'USD',
        ]);

        return new ServiceUpgrade([
            'service_id' => $service->id,
            'product_id' => $new->product->id,
            'plan_id' => $new->plan->id,
        ]);
    }
}
