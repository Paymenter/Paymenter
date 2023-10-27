<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Category, Invoice, Order, Product, ProductPrice, User};
use Livewire\Livewire;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $categorie;
    protected $product;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->categorie = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $this->categorie->id,
        ]);
        ProductPrice::factory()->create([
            'product_id' => $this->product->id,
            'type' => 'one-time',
            'monthly' => 0,
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanPlaceProductInCart()
    {
        Livewire::test('product', ['product' => $this->product])
            ->call('addToCart', $this->product->id)->assertSet('added', true);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanContinueToInvoice()
    {
        Livewire::test('product', ['product' => $this->product])
            ->call('addToCart', $this->product->id)->assertSet('added', true);

        Livewire::actingAs($this->user)
            ->test('checkout', ['product' => $this->product])
            ->call('pay')->assertRedirect(route('clients.home'));
        
    }
}
