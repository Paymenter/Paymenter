<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\{User, Order, Products, Categories, Invoices};

class OrderTest extends TestCase
{

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
    public function test_can_place_product_in_cart()
    {
        $response = $this->actingAs($this->user)->get(route('checkout.add') . '?id=' . $this->product->id);

        $response->assertStatus(302);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_continue_to_invoice()
    {
        $response = $this->actingAs($this->user)->get(route('checkout.add') . '?id=' . $this->product->id);

        $response->assertStatus(302);

        $response = $this->actingAs($this->user)->post(route('checkout.pay'));

        $response->assertStatus(302);

        // Get generated invoice id
        $invoice_id = Invoices::where('user_id', $this->user->id)->first()->id;

        $response->assertRedirect(route('clients.invoice.show', $invoice_id));
    }
}
