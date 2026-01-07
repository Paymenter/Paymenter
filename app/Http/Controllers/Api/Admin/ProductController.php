<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Admin\Products\CreateProductRequest;
use App\Http\Requests\Api\Admin\Products\DeleteProductRequest;
use App\Http\Requests\Api\Admin\Products\GetProductRequest;
use App\Http\Requests\Api\Admin\Products\GetProductsRequest;
use App\Http\Requests\Api\Admin\Products\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'Products', weight: 1)]
class ProductController extends ApiController
{
    protected const INCLUDES = [
        'name',
        'description',
        'category',
        'prices',
        'enabled',
        'slug',
        'stock',
    ];

    /**
     * List Products
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(GetProductsRequest $request)
    {
        // Fetch products with pagination
        $products = QueryBuilder::for(Product::class)
            ->allowedFilters(['name', 'slug'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'name', 'slug', 'created_at'])
            ->simplePaginate(request('per_page', 15));

        // Return the products as a JSON
        return ProductResource::collection($products);
    }

    /**
     * Create a new product
     */
    public function store(CreateProductRequest $request)
    {
        // Validate and create the product
        $product = Product::create($request->validated());

        // Return the created product as a JSON response
        return new ProductResource($product);
    }

    /**
     * Show a specific product
     */
    public function show(GetProductRequest $request, Product $product)
    {
        $product = QueryBuilder::for(Product::class)
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->findOrFail($product->id);

        // Return the product as a JSON response
        return new ProductResource($product);
    }

    /**
     * Update a specific product
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        // Validate and update the product
        $product->update($request->validated());

        // Return the updated product as a JSON response
        return new ProductResource($product);
    }

    /**
     * Delete a specific product
     */
    public function destroy(DeleteProductRequest $request, Product $product)
    {
        // Delete the product
        $product->delete();

        return $this->returnNoContent();
    }
}
