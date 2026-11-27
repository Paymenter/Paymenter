<?php

namespace Tests\Feature;

use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartDomainLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_removing_a_hosting_item_cascades_to_its_required_domain_item(): void
    {
        $product = $this->createProduct();
        $cart = Cart::create(['currency_code' => 'USD']);

        $hosting = $cart->items()->create([
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'config_options' => [],
            'checkout_config' => ['domain' => 'example.com'],
            'quantity' => 1,
        ]);

        $domain = $cart->items()->create([
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'config_options' => [],
            'checkout_config' => ['domain' => 'example.com'],
            'quantity' => 1,
            'parent_id' => $hosting->id,
        ]);

        $this->assertTrue($domain->parent->is($hosting));
        $this->assertTrue($hosting->children->contains($domain));

        $hosting->delete();

        $this->assertDatabaseMissing('cart_items', ['id' => $domain->id]);
    }
}
