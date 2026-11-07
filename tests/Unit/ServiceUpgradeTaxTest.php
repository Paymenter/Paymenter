<?php

namespace Tests\Unit;

use App\Models\Price;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceUpgradeTaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_upgrade_price_includes_exclusive_tax(): void
    {
        config(['settings.tax_enabled' => true, 'settings.tax_type' => 'exclusive']);
        TaxRate::create(['name' => 'VAT', 'rate' => 21, 'country' => 'all']);

        $user = User::factory()->create();
        $old = $this->createProduct();
        $new = $this->createProduct();
        Price::where('plan_id', $new->plan->id)->update(['price' => 20.00]);

        $service = Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $old->plan->id,
            'product_id' => $old->product->id,
            'status' => 'active',
            'expires_at' => now()->addDays(30),
            'currency_code' => 'USD',
        ]);

        $upgrade = new ServiceUpgrade([
            'service_id' => $service->id,
            'product_id' => $new->product->id,
            'plan_id' => $new->plan->id,
        ]);

        // $10 difference for the full period, plus 21% VAT on top
        $this->assertEquals(12.10, $upgrade->calculatePrice()->price);
    }
}
