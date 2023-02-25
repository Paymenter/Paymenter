<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{Categories, Invoices, Order, Products, User};

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
        $this->categorie = Categories::factory()->create();
        $this->product = Products::factory()->create([
            'category_id' => $this->categorie->id,
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
        $response = $this->actingAs($this->user)->get(route( 'checkout.add', $this->product->id));

        $response->assertStatus(302);

        $response = $this->actingAs($this->user)->post(route('checkout.pay'));

        $response->assertStatus(302);

        // Get generated invoice id
        $invoice = Invoices::where('user_id', $this->user->id)->first();

        $this->assertNotNull($invoice);

        $response->assertRedirect(route('clients.invoice.show', $invoice));
    }
}
