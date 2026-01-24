<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Admin\Products\GetProductRequest;
use App\Http\Requests\Api\Admin\Products\GetProductsRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'Products', weight: 2)]
class ProductController extends ApiController
{
    protected const INCLUDES = [
        'category',
        'plans.prices',
        'services',
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
            ->allowedFilters(['name', 'category_id', 'server_id', 'hidden', 'allow_quantity'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'created_at', 'updated_at', 'name', 'sort', 'stock'])
            ->simplePaginate(request('per_page', 15));

        // Return the products as a JSON response
        return ProductResource::collection($products);
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
}
