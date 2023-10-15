<?php

namespace App\Http\Controllers\API\Website;

use App\Classes\API;
use App\Models\Product;
use App\Http\Controllers\API\Controller;

class ProductController extends Controller
{
    /**
     * Get all products.
     */
    public function getProducts()
    {
        $products = Product::paginate(config('app.pagination'));

        return $this->success('Products successfully retrieved.', API::repaginate($products));
    }
}
