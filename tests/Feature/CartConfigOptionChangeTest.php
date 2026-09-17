<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\ConfigOption;
use App\Models\Plan;
use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Once;
use Livewire\Livewire;
use Tests\TestCase;

class CartConfigOptionChangeTest extends TestCase
{
    use RefreshDatabase;

    private $product = null;

    private ConfigOption $option;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = $this->createProduct();

        $this->option = ConfigOption::create([
            'name' => 'Location',
            'type' => 'radio',
            'sort' => 0,
        ]);
        $this->option->products()->attach($this->product->product->id);

        foreach (['Amsterdam', 'Frankfurt'] as $sort => $name) {
            $this->createOptionValue($name, $sort);
        }
    }

    private function createOptionValue(string $name, int $sort): ConfigOption
    {
        $value = ConfigOption::create([
            'name' => $name,
            'sort' => $sort,
            'parent_id' => $this->option->id,
        ]);

        $plan = Plan::factory()->create([
            'priceable_id' => $value->id,
            'priceable_type' => ConfigOption::class,
            'name' => $name,
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
        ]);

        Price::factory()->create([
            'plan_id' => $plan->id,
            'price' => 1.00,
            'currency_code' => 'USD',
        ]);

        return $value;
    }

    private function addToCart(ConfigOption $value): array
    {
        Livewire::test('products.checkout', [
            'category' => $this->product->product->category,
            'product' => $this->product->product->slug,
        ])
            ->set('configOptions.' . $this->option->id, $value->id)
            ->call('checkout');

        $cart = Cart::firstOrFail();
        Once::flush();

        return [$cart, $cart->items()->firstOrFail()];
    }

    private function editCartItem(Cart $cart, $item)
    {
        return Livewire::withCookie('cart', $cart->ulid)->test('products.checkout', [
            'category' => $this->product->product->category,
            'product' => $this->product->product->slug,
            'cartProductKey' => $item->id,
        ]);
    }

    public function test_cart_item_can_be_edited_after_its_option_value_is_removed(): void
    {
        $values = $this->option->children()->get();
        [$cart, $item] = $this->addToCart($values[0]);

        // The option value the item was ordered with is removed from the product.
        $values[0]->delete();

        $this->editCartItem($cart, $item)
            ->assertSet('configOptions.' . $this->option->id, $values[1]->id)
            ->call('checkout')
            ->assertHasNoErrors();

        $this->assertSame(1, $cart->items()->count());
        $this->assertSame($values[1]->id, $cart->items()->first()->config_options[0]['value']);
    }

    public function test_cart_item_keeps_its_option_value_when_it_is_still_available(): void
    {
        $values = $this->option->children()->get();
        [$cart, $item] = $this->addToCart($values[1]);

        $this->editCartItem($cart, $item)
            ->assertSet('configOptions.' . $this->option->id, $values[1]->id)
            ->call('checkout')
            ->assertHasNoErrors();

        $this->assertSame(1, $cart->items()->count());
        $this->assertSame($values[1]->id, $cart->items()->first()->config_options[0]['value']);
    }
}
