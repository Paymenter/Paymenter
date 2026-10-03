<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "creating" event.
     *
     * @return void
     */
    public function creating(Product $product)
    {
        // Place new products at the end of their category instead of in front of it
        $product->sort ??= $this->nextSort($product);
    }

    /**
     * Handle the Product "updating" event.
     *
     * @return void
     */
    public function updating(Product $product)
    {
        // The sort value of the previous category says nothing about the new one
        if ($product->isDirty('category_id') && !$product->isDirty('sort')) {
            $product->sort = $this->nextSort($product);
        }
    }

    /**
     * Get the sort value which places the product last within its category.
     */
    private function nextSort(Product $product): int
    {
        return (int) Product::where('category_id', $product->category_id)->max('sort') + 1;
    }
}
