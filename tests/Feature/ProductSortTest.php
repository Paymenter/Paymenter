<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_products_are_appended_to_their_category(): void
    {
        $category = Category::factory()->create();

        $first = Product::factory()->create(['category_id' => $category->id]);
        $second = Product::factory()->create(['category_id' => $category->id]);

        $this->assertSame(1, $first->sort);
        $this->assertSame(2, $second->sort);
    }

    public function test_products_are_sorted_per_category(): void
    {
        $product = Product::factory()->create(['category_id' => Category::factory()->create()->id]);
        $other = Product::factory()->create(['category_id' => Category::factory()->create()->id]);

        $this->assertSame(1, $product->sort);
        $this->assertSame(1, $other->sort);
    }

    public function test_an_explicit_sort_is_kept(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'sort' => 50,
        ]);

        $this->assertSame(50, $product->sort);
    }

    public function test_moving_a_product_appends_it_to_the_new_category(): void
    {
        $category = Category::factory()->create();
        $target = Category::factory()->create();

        Product::factory()->create(['category_id' => $target->id]);
        Product::factory()->create(['category_id' => $target->id]);

        $product = Product::factory()->create(['category_id' => $category->id]);
        $product->update(['category_id' => $target->id]);

        $this->assertSame(3, $product->sort);
    }

    public function test_moving_a_product_keeps_an_explicit_sort(): void
    {
        $category = Category::factory()->create();
        $target = Category::factory()->create();

        $product = Product::factory()->create(['category_id' => $category->id]);
        $product->update(['category_id' => $target->id, 'sort' => 7]);

        $this->assertSame(7, $product->sort);
    }

    public function test_new_categories_are_appended_to_their_level(): void
    {
        $first = Category::factory()->create();
        $second = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $first->id]);

        $this->assertSame(1, $first->sort);
        $this->assertSame(2, $second->sort);
        $this->assertSame(1, $child->sort);
    }
}
