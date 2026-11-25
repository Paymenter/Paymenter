<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\ConfigOption;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartConfigOptionPriceTest extends TestCase
{
    use RefreshDatabase;

    private $product = null;

    private ConfigOption $option;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = $this->createProduct();

        $this->option = ConfigOption::create([
            'name' => 'Extra IPv4',
            'type' => 'select',
            'sort' => 0,
        ]);
        $this->option->products()->attach($this->product->product->id);
    }

    private function createValue(string $name, ?array $plan = null): ConfigOption
    {
        $value = ConfigOption::create([
            'name' => $name,
            'sort' => 0,
            'parent_id' => $this->option->id,
        ]);

        if ($plan) {
            $created = Plan::factory()->create([
                'priceable_id' => $value->id,
                'priceable_type' => ConfigOption::class,
                'name' => $name,
                'billing_unit' => $plan['billing_unit'] ?? 'month',
                'billing_period' => $plan['billing_period'] ?? 1,
                'type' => 'recurring',
            ]);

            Price::factory()->create([
                'plan_id' => $created->id,
                'price' => $plan['price'] ?? 2.00,
                'currency_code' => $plan['currency'] ?? 'USD',
            ]);
        }

        return $value;
    }

    private function cartWith(ConfigOption $value): Cart
    {
        $cart = Cart::create(['currency_code' => 'USD']);
        $cart->items()->create([
            'product_id' => $this->product->product->id,
            'plan_id' => $this->product->plan->id,
            'config_options' => [['option_id' => $this->option->id, 'value' => $value->id]],
            'checkout_config' => [],
            'quantity' => 1,
        ]);

        return $cart->fresh();
    }

    private function productPageTotal(ConfigOption $value)
    {
        return Livewire::test('products.checkout', [
            'category' => $this->product->product->category,
            'product' => $this->product->product->slug,
        ])
            ->set('configOptions.' . $this->option->id, $value->id)
            ->get('total');
    }

    public function test_value_without_plans_is_free(): void
    {
        $value = $this->createValue('None');

        $price = $this->cartWith($value)->items->first()->price;

        $this->assertTrue($price->available);
        $this->assertEquals(10.00, (float) $price->price);

        $total = $this->productPageTotal($value);
        $this->assertTrue($total->available);
        $this->assertEquals(10.00, (float) $total->price);
    }

    public function test_priced_value_is_added_to_the_item(): void
    {
        $value = $this->createValue('One', ['price' => 2.00]);

        $price = $this->cartWith($value)->items->first()->price;

        $this->assertTrue($price->available);
        $this->assertEquals(12.00, (float) $price->price);
        $this->assertEquals(12.00, (float) $this->productPageTotal($value)->price);
    }

    public function test_value_priced_only_for_another_billing_period_is_unavailable(): void
    {
        $value = $this->createValue('One', ['billing_unit' => 'year']);

        $this->assertFalse($this->cartWith($value)->items->first()->price->available);
        $this->assertFalse($this->productPageTotal($value)->available);
    }

    public function test_value_priced_only_in_another_currency_is_unavailable(): void
    {
        Currency::create(['code' => 'EUR', 'name' => 'Euro', 'suffix' => 'EUR', 'format' => '1.000,00']);
        $value = $this->createValue('One', ['currency' => 'EUR']);

        $this->assertFalse($this->cartWith($value)->items->first()->price->available);
    }
}
