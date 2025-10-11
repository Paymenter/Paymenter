<?php

namespace App\Observers;

use App\Events\Cart as CartEvent;
use App\Models\Cart;

class CartObserver
{
    /**
     * Handle the Cart "creating" event.
     */
    public function creating(Cart $cart): void
    {
        event(new CartEvent\Creating($cart));
    }

    /**
     * Handle the Cart "created" event.
     */
    public function created(Cart $cart): void
    {
        event(new CartEvent\Created($cart));
    }

    /**
     * Handle the Cart "updating" event.
     */
    public function updating(Cart $cart): void
    {
        event(new CartEvent\Updating($cart));
    }

    /**
     * Handle the Cart "updated" event.
     */
    public function updated(Cart $cart): void
    {
        event(new CartEvent\Updated($cart));
    }

    /**
     * Handle the Cart "deleting" event.
     */
    public function deleting(Cart $cart): void
    {
        event(new CartEvent\Deleting($cart));
    }

    /**
     * Handle the Cart "deleted" event.
     */
    public function deleted(Cart $cart): void
    {
        event(new CartEvent\Deleted($cart));
    }
}
