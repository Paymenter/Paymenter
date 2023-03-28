<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Category, Invoice, Order, Product, ProductPrice, User};

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
        $response = $this->actingAs($this->user)->get(route('checkout.add', $this->product->id));

        $response->assertStatus(302);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanContinueToInvoice()
    {
        $response = $this->actingAs($this->user)->get(route('checkout.add', $this->product->id));

        $response->assertStatus(302);

        $response = $this->actingAs($this->user)->post(route('checkout.pay'));

        $response->assertStatus(302);

        $invoice = Invoice::where('user_id', $this->user->id)->first();

        $this->assertNotNull($invoice);

        $response->assertRedirect(route('clients.home'));
    }
}
