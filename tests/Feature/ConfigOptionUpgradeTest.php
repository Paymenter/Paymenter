<?php

namespace Tests\Feature;

use App\Models\ConfigOption;
use App\Models\Plan;
use App\Models\Price;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Models\User;
use App\Services\ServiceUpgrade\ServiceUpgradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConfigOptionUpgradeTest extends TestCase
{
    use RefreshDatabase;

    private $product = null;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = $this->createProduct();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * A storage option at $0.05 per GB, upgradable, running from 100 to 5000 in steps of 10.
     */
    private function createStorageOption(array $attributes = []): ConfigOption
    {
        $option = ConfigOption::create(array_merge([
            'name' => 'Storage',
            'env_variable' => 'STORAGE',
            'type' => 'number',
            'min' => 100,
            'max' => 5000,
            'step' => 10,
            'upgradable' => true,
        ], $attributes));

        $option->products()->attach($this->product->product->id);

        $child = ConfigOption::create([
            'name' => 'Price per GB',
            'type' => 'number',
            'parent_id' => $option->id,
        ]);

        $plan = Plan::factory()->create([
            'priceable_id' => $child->id,
            'priceable_type' => ConfigOption::class,
            'name' => 'Test Plan',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
        ]);
        Price::factory()->create([
            'plan_id' => $plan->id,
            'price' => 0.05,
            'currency_code' => 'USD',
        ]);

        return $option->refresh();
    }

    /**
     * An active service holding $quantity GB, with $days left of its month.
     */
    private function createServiceWith(ConfigOption $option, $quantity, int $days = 14): Service
    {
        $service = Service::factory()->create([
            'user_id' => $this->user->id,
            'plan_id' => $this->product->plan->id,
            'product_id' => $this->product->product->id,
            'status' => 'active',
            'currency_code' => 'USD',
            'price' => 10.00,
            'expires_at' => now()->addDays($days),
        ]);

        $service->configs()->create([
            'config_option_id' => $option->id,
            'config_value_id' => $option->children->first()->id,
            'value' => $quantity,
        ]);

        return $service->refresh();
    }

    private function quote(Service $service, ConfigOption $option, $quantity): float
    {
        $upgrade = new ServiceUpgrade([
            'service_id' => $service->id,
            'product_id' => $service->product_id,
            'plan_id' => $service->plan_id,
        ]);
        $upgrade->save();
        $upgrade->configs()->create([
            'config_option_id' => $option->id,
            'config_value_id' => $option->children->first()->id,
            'value' => $quantity,
        ]);

        return round((float) $upgrade->refresh()->calculatePrice()->price, 2);
    }

    public function test_upgrading_the_amount_is_prorated_over_the_remaining_days(): void
    {
        $option = $this->createStorageOption();
        $service = $this->createServiceWith($option, 100, days: 14);

        // (300 - 100) x $0.05 = $10.00 a month, of which 14 of 30 days remain
        $this->assertEquals(4.67, $this->quote($service, $option, 300));
    }

    public function test_downgrading_the_amount_gives_a_prorated_credit(): void
    {
        $option = $this->createStorageOption();
        $service = $this->createServiceWith($option, 300, days: 14);

        $this->assertEquals(-4.67, $this->quote($service, $option, 100));
    }

    public function test_an_unchanged_amount_costs_nothing(): void
    {
        $option = $this->createStorageOption();
        $service = $this->createServiceWith($option, 300, days: 14);

        $this->assertEquals(0.0, $this->quote($service, $option, 300));
    }

    public function test_the_base_price_is_only_charged_when_the_option_is_added(): void
    {
        $option = $this->createStorageOption();

        // $2.00 a month for having the option at all
        $basePlan = Plan::factory()->create([
            'priceable_id' => $option->id,
            'priceable_type' => ConfigOption::class,
            'name' => 'Base',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
        ]);
        Price::factory()->create([
            'plan_id' => $basePlan->id,
            'price' => 2.00,
            'currency_code' => 'USD',
        ]);
        $option->refresh();

        // Already on the service: the base price sits in both amounts and cancels out
        $existing = $this->createServiceWith($option, 100, days: 14);
        $this->assertEquals(4.67, $this->quote($existing, $option, 300));

        // Added now: 300 x 0.05 + 2.00 = $17.00 a month, prorated over 14 of 30 days
        $fresh = Service::factory()->create([
            'user_id' => $this->user->id,
            'plan_id' => $this->product->plan->id,
            'product_id' => $this->product->product->id,
            'status' => 'active',
            'currency_code' => 'USD',
            'price' => 10.00,
            'expires_at' => now()->addDays(14),
        ]);
        $this->assertEquals(7.93, $this->quote($fresh->refresh(), $option, 300));
    }

    public function test_completing_the_upgrade_stores_the_new_amount_and_reprices_the_service(): void
    {
        $option = $this->createStorageOption();
        $service = $this->createServiceWith($option, 100, days: 14);

        $upgrade = new ServiceUpgrade([
            'service_id' => $service->id,
            'product_id' => $service->product_id,
            'plan_id' => $service->plan_id,
        ]);
        $upgrade->save();
        $upgrade->configs()->create([
            'config_option_id' => $option->id,
            'config_value_id' => $option->children->first()->id,
            'value' => 300,
        ]);

        (new ServiceUpgradeService)->handle($upgrade->refresh());

        $this->assertDatabaseHas('service_configs', [
            'configurable_id' => $service->id,
            'config_option_id' => $option->id,
            'value' => 300,
        ]);

        // Renewals now cost 10 (plan) + 300 x 0.05 = 25
        $this->assertEquals('25.00', $service->refresh()->calculatePrice());
    }

    public function test_upgrade_screen_starts_from_the_amount_the_customer_has(): void
    {
        $option = $this->createStorageOption();
        $service = $this->createServiceWith($option, 300, days: 14);

        Livewire::test('services.upgrade', ['service' => $service])
            ->assertSet('configOptions.' . $option->id, '300')
            ->assertSeeHtml('type="number"')
            ->set('configOptions.' . $option->id, 500)
            ->assertSeeText('Storage');
    }

    public function test_upgrade_screen_validates_the_range_and_increment(): void
    {
        $option = $this->createStorageOption();
        $service = $this->createServiceWith($option, 100, days: 14);

        $component = Livewire::test('services.upgrade', ['service' => $service]);

        $component->set('configOptions.' . $option->id, 105)->call('doUpgrade')->assertHasErrors('configOptions.' . $option->id);
        $component->set('configOptions.' . $option->id, 6000)->call('doUpgrade')->assertHasErrors('configOptions.' . $option->id);
        $component->set('configOptions.' . $option->id, 300)->call('doUpgrade')->assertHasNoErrors();
    }

    public function test_the_customer_can_raise_the_amount_and_is_invoiced_the_difference(): void
    {
        $option = $this->createStorageOption();
        $service = $this->createServiceWith($option, 100, days: 14);

        Livewire::test('services.upgrade', ['service' => $service])
            ->set('configOptions.' . $option->id, 300)
            ->call('doUpgrade')
            ->assertHasNoErrors();

        $upgrade = ServiceUpgrade::where('service_id', $service->id)->first();

        $this->assertNotNull($upgrade);
        $this->assertEquals(300, (float) $upgrade->configs->first()->value);

        // The invoice covers the 14 remaining days of the extra 200 GB, not a full month
        $invoice = $upgrade->invoice;
        $this->assertNotNull($invoice);
        $this->assertEquals(4.67, round((float) $invoice->items->first()->price, 2));

        // Nothing changes on the service until that invoice is settled
        $this->assertEquals(100, (float) $service->refresh()->configs->first()->value);
    }

    public function test_submitting_the_same_amount_is_rejected_as_no_change(): void
    {
        $option = $this->createStorageOption();
        $service = $this->createServiceWith($option, 300, days: 14);

        Livewire::test('services.upgrade', ['service' => $service])
            ->set('configOptions.' . $option->id, 300)
            ->call('doUpgrade');

        $this->assertDatabaseCount('service_upgrades', 0);
    }

    public function test_a_number_option_without_a_unit_price_is_not_upgradable(): void
    {
        $option = ConfigOption::create([
            'name' => 'Nodes',
            'env_variable' => 'NODES',
            'type' => 'number',
            'upgradable' => true,
        ]);
        $option->products()->attach($this->product->product->id);

        $product = $this->product->product->refresh();

        // Nothing to bill for, so it never reaches the upgrade screen and does not make the service upgradable
        $this->assertFalse($product->upgradableConfigOptions->contains('id', $option->id));
        $this->assertEquals(0, $product->upgradableConfigOptions()->count());

        $service = Service::factory()->create([
            'user_id' => $this->user->id,
            'plan_id' => $this->product->plan->id,
            'product_id' => $product->id,
            'status' => 'active',
            'currency_code' => 'USD',
            'price' => 10.00,
            'expires_at' => now()->addDays(14),
        ]);

        $this->assertFalse($service->refresh()->upgradable);
    }

    public function test_a_number_option_becomes_upgradable_once_it_has_a_unit_price(): void
    {
        $option = $this->createStorageOption();
        $product = $this->product->product->refresh();

        $this->assertTrue($product->upgradableConfigOptions->contains('id', $option->id));

        $service = $this->createServiceWith($option, 100, days: 14);

        $this->assertTrue($service->upgradable);

        Livewire::test('services.upgrade', ['service' => $service])
            ->assertSeeText('Storage');
    }
}
