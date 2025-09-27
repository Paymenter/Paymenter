<?php

namespace App\Observers;

use App\Events\CartItem as CartItemEvent;
use App\Models\CartItem;

class CartItemObserver
{
    /**
     * Handle the CartItem "creating" event.
     */
    public function creating(CartItem $cartItem): void
    {
        event(new CartItemEvent\Creating($cartItem));
    }

    /**
     * Handle the CartItem "created" event.
     */
    public function created(CartItem $cartItem): void
    {
        event(new CartItemEvent\Created($cartItem));
    }

    /**
     * Handle the CartItem "updating" event.
     */
    public function updating(CartItem $cartItem): void
    {
        event(new CartItemEvent\Updating($cartItem));
    }

    /**
     * Handle the CartItem "updated" event.
     */
    public function updated(CartItem $cartItem): void
    {
        event(new CartItemEvent\Updated($cartItem));
    }

    /**
     * Handle the CartItem "deleting" event.
     */
    public function deleting(CartItem $cartItem): void
    {
        event(new CartItemEvent\Deleting($cartItem));
    }

    /**
     * Handle the CartItem "deleted" event.
     */
    public function deleted(CartItem $cartItem): void
    {
        event(new CartItemEvent\Deleted($cartItem));
    }
}
