<?php

namespace Tests\Feature;

use Livewire\Livewire;
use Tests\TestCase;

class CartTest extends TestCase
{
    public function test_can_visit_cart_page()
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
    }

    public function test_cannot_checkout_without_items()
    {
        Livewire::test('cart')
            ->call('checkout')
            ->assertDispatched('notify');
    }

    private function createCartItem()
    {
        return [
            'id' => 1,
            'name' => 'Test Product',
            'price' => 100,
            'quantity' => 1,
        ];
    }
}
