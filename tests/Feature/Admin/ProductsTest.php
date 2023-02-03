<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Products;
use App\Models\Categories;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;
    protected $user;
    protected $category;
    protected $product;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => 1]);
        $this->category = Categories::factory()->create();
        $this->product = Products::factory()->create([
            'category_id' => $this->category->id,
        ]);
    }

    /**
     * Test if admin can create a product.
     *
     * @return void
     */
    public function testIfAdminCanCreateAProduct()
    {
        $image = UploadedFile::fake()->image('test.jpg');
        $response = $this->actingAs($this->user)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'price' => 100,
            'image' => $image,
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'price' => 100,
            'category_id' => $this->category->id,
        ]);
    }

    /**
     * Test if admin can update a product.
     *
     * @return void
     */
    public function testIfAdminCanUpdateAProduct()
    {
        $image = UploadedFile::fake()->image('test2.jpg');

        $response = $this->actingAs($this->user)->post(route('admin.products.update', $this->product->id), [
            'name' => 'Test Product Updated',
            'description' => 'Test Product Description Updated',
            'price' => 200,
            'image' => $image,
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product Updated',
            'description' => 'Test Product Description Updated',
            'price' => 200,
            'category_id' => $this->category->id,
        ]);
    }

    /**
     * Test if admin can delete a product.
     *
     * @return void
     */
    public function testIfAdminCanDeleteAProduct()
    {
        $response = $this->actingAs($this->user)->delete(route('admin.products.destroy', $this->product->id));

        $response->assertStatus(302);

        $this->assertDatabaseMissing('products', [
            'name' => 'Test Product',
            'description' => 'Test Product Description',
            'price' => 100,
            'category_id' => $this->category->id,
        ]);
    }
}
