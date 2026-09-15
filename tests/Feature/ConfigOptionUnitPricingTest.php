<?php

namespace Tests\Feature;

use App\Helpers\ExtensionHelper;
use App\Models\Cart;
use App\Models\ConfigOption;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\Price;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Models\User;
use App\Services\ServiceUpgrade\ServiceUpgradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Once;
use Livewire\Livewire;
use Tests\TestCase;

class ConfigOptionUnitPricingTest extends TestCase
{
    use RefreshDatabase;

    private $product = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = $this->createProduct();
    }

    /**
     * Create a number config option charging $unitPrice for every entered unit.
     */
    private function createNumberOption(float $unitPrice, array $attributes = [], ?float $basePrice = null): ConfigOption
    {
        $option = ConfigOption::create(array_merge([
            'name' => 'Storage',
            'env_variable' => 'STORAGE',
            'type' => 'number',
            'min' => 100,
            'max' => 5000,
            'step' => 10,
        ], $attributes));

        $option->products()->attach($this->product->product->id);

        $child = ConfigOption::create([
            'name' => 'Price per GB',
            'type' => 'number',
            'parent_id' => $option->id,
        ]);

        $this->createPlanFor($child, $unitPrice);

        if ($basePrice !== null) {
            $this->createPlanFor($option, $basePrice);
        }

        return $option->refresh();
    }

    private function createPlanFor(ConfigOption $option, float $price): void
    {
        $plan = Plan::factory()->create([
            'priceable_id' => $option->id,
            'priceable_type' => ConfigOption::class,
            'name' => 'Test Plan',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
        ]);

        Price::factory()->create([
            'plan_id' => $plan->id,
            'price' => $price,
            'currency_code' => 'USD',
        ]);
    }

    public function test_number_option_without_pricing_stays_free(): void
    {
        $option = ConfigOption::create([
            'name' => 'Hostname number',
            'env_variable' => 'NODES',
            'type' => 'number',
        ]);
        $option->products()->attach($this->product->product->id);

        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->set('configOptions.' . $option->id, 8)
            ->assertSet('total.price', 10.0);
    }

    public function test_number_option_multiplies_unit_price_by_entered_amount(): void
    {
        $option = $this->createNumberOption(0.75, ['name' => 'RAM', 'env_variable' => 'RAM', 'min' => 1, 'max' => 64, 'step' => 1]);

        // 10 (plan) + 8 * 0.75 = 16
        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->assertSet('configOptions.' . $option->id, 1.0)
            ->set('configOptions.' . $option->id, 8)
            ->assertSet('total.price', 16.0);
    }

    public function test_number_option_adds_its_base_price_once(): void
    {
        $option = $this->createNumberOption(0.05, basePrice: 2.0);

        // 10 (plan) + 2 (base) + 500 * 0.05 = 37
        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->assertSeeHtml('type="number"')
            ->assertSeeHtml('min="100"')
            ->assertSeeHtml('max="5000"')
            ->assertSeeHtml('step="10"')
            ->assertSeeText('Storage - $0.05 per unit')
            ->set('configOptions.' . $option->id, 500)
            ->assertSet('total.price', 37.0);
    }

    public function test_number_option_renders_a_slider_when_enabled(): void
    {
        $option = $this->createNumberOption(0.05, ['show_as_slider' => true]);

        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->assertSeeHtml('type="range"')
            ->assertSeeHtml('min="100"')
            ->assertSeeHtml('max="5000"')
            ->assertSeeHtml('step="10"')
            ->assertSeeText('Storage - $0.05 per unit')
            // Dragging the slider feeds the same property the plain input uses
            ->set('configOptions.' . $option->id, 500)
            ->assertSet('total.price', 35.0);

        $this->assertTrue($option->hasSlider());
    }

    public function test_slider_falls_back_to_an_input_without_a_full_range(): void
    {
        $option = $this->createNumberOption(0.05, ['show_as_slider' => true, 'max' => null]);

        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->assertDontSeeHtml('type="range"')
            ->assertSeeHtml('type="number"');

        $this->assertFalse($option->hasSlider());
    }

    public function test_number_option_rejects_amounts_outside_its_range_or_increment(): void
    {
        $option = $this->createNumberOption(0.05);

        $component = Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug]);

        $component->set('configOptions.' . $option->id, 50)->call('checkout')->assertHasErrors('configOptions.' . $option->id);
        $component->set('configOptions.' . $option->id, 6000)->call('checkout')->assertHasErrors('configOptions.' . $option->id);
        $component->set('configOptions.' . $option->id, 105)->call('checkout')->assertHasErrors('configOptions.' . $option->id);
        $component->set('configOptions.' . $option->id, 110)->call('checkout')->assertHasNoErrors();
    }

    public function test_cart_item_keeps_charging_per_unit(): void
    {
        $option = $this->createNumberOption(0.05, basePrice: 2.0);

        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->set('configOptions.' . $option->id, 500)
            ->call('checkout');

        Once::flush();

        $item = Cart::first()->items()->first();

        $this->assertEquals(37.0, (float) $item->price->price);
    }

    public function test_ordering_stores_the_amount_on_the_service(): void
    {
        $option = $this->createNumberOption(0.05, basePrice: 2.0);
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->set('configOptions.' . $option->id, 500)
            ->call('checkout');

        Once::flush();

        Livewire::withCookie('cart', Cart::first()->ulid)->test('cart')->call('checkout');

        $service = Service::first();

        $this->assertNotNull($service);
        $this->assertDatabaseHas('service_configs', [
            'configurable_id' => $service->id,
            'config_option_id' => $option->id,
            'config_value_id' => $option->children->first()->id,
            'value' => 500,
        ]);

        // 10 (plan) + 2 (base) + 500 * 0.05 = 37
        $this->assertEquals('37.00', $service->calculatePrice());
        $this->assertEquals(500, ExtensionHelper::getServiceProperties($service)['STORAGE']);
    }

    public function test_setup_fee_is_charged_per_unit_too(): void
    {
        $option = $this->createNumberOption(0.05, ['min' => 1, 'max' => 1000, 'step' => 1]);
        // $0.10 one-off per GB on top of the recurring $0.05
        $option->children->first()->plans->first()->prices()->update(['setup_fee' => 0.10]);
        $option->refresh();

        $price = $option->priceForQuantity(200, 1, 'month', 'USD');

        $this->assertEquals(10.0, $price->price);
        $this->assertEquals(20.0, $price->setup_fee);
    }

    public function test_an_option_without_a_price_in_the_chosen_currency_makes_the_item_unavailable(): void
    {
        Currency::create(['code' => 'EUR', 'name' => 'Euro', 'prefix' => '&euro;', 'suffix' => '', 'format' => '1.000,00']);
        $this->product->plan->prices()->create(['price' => 9.00, 'currency_code' => 'EUR']);

        // The unit price only exists in USD
        $option = $this->createNumberOption(0.05);

        $price = $option->priceForQuantity(500, 1, 'month', 'EUR');

        $this->assertFalse($price->available);
        $this->assertEquals(0.0, $price->price);
    }

    public function test_a_coupon_discounts_the_per_unit_total(): void
    {
        $option = $this->createNumberOption(0.05, basePrice: 2.0);
        $user = User::factory()->create();
        $this->actingAs($user);

        $coupon = Coupon::create([
            'code' => 'HALF',
            'type' => 'percentage',
            'value' => 50,
            'applies_to' => 'all',
        ]);

        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->set('configOptions.' . $option->id, 500)
            ->call('checkout');

        Once::flush();

        $cart = Cart::first();
        $cart->update(['coupon_id' => $coupon->id]);

        // 50% off the full 37.00, per-unit charges included
        $this->assertEquals(18.5, (float) $cart->refresh()->items()->first()->price->price);
    }

    public function test_product_quantity_multiplies_the_per_unit_total(): void
    {
        $option = $this->createNumberOption(0.05, basePrice: 2.0);

        Livewire::test('products.checkout', ['category' => $this->product->product->category, 'product' => $this->product->product->slug])
            ->set('configOptions.' . $option->id, 500)
            ->call('checkout');

        Once::flush();

        $item = Cart::first()->items()->first();
        $item->update(['quantity' => 3]);

        // Each unit of the product carries its own 37.00
        $this->assertEquals(111.0, round($item->refresh()->price->total * $item->quantity, 2));
    }

    public function test_number_config_survives_a_service_upgrade(): void
    {
        $option = $this->createNumberOption(0.05, basePrice: 2.0);
        $user = User::factory()->create();

        $service = Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $this->product->plan->id,
            'product_id' => $this->product->product->id,
            'status' => 'active',
            'currency_code' => 'USD',
            'price' => 10.00,
        ]);
        $service->configs()->create([
            'config_option_id' => $option->id,
            'config_value_id' => $option->children->first()->id,
            'value' => 500,
        ]);

        $upgradeProduct = $this->createProduct(['name' => 'Test Product', 'slug' => 'upgrade-product']);

        $upgrade = ServiceUpgrade::create([
            'service_id' => $service->id,
            'product_id' => $upgradeProduct->product->id,
            'plan_id' => $upgradeProduct->plan->id,
        ]);

        (new ServiceUpgradeService)->handle($upgrade);

        $this->assertDatabaseHas('service_configs', [
            'configurable_id' => $service->id,
            'config_option_id' => $option->id,
            'value' => 500,
        ]);
    }

    public function test_service_renewal_keeps_charging_per_unit(): void
    {
        $option = $this->createNumberOption(0.05, basePrice: 2.0);
        $user = User::factory()->create();

        $service = Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $this->product->plan->id,
            'product_id' => $this->product->product->id,
            'status' => 'active',
            'currency_code' => 'USD',
            'price' => 10.00,
        ]);

        $service->configs()->create([
            'config_option_id' => $option->id,
            'config_value_id' => $option->children->first()->id,
            'value' => 500,
        ]);

        $service->refresh();

        // 10 (plan) + 2 (base) + 500 * 0.05 = 37
        $this->assertEquals('37.00', $service->calculatePrice());
    }
}
