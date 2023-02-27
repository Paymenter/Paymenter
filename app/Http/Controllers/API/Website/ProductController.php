<?php

namespace App\Http\Controllers\API\Website;

use App\Classes\API;
use App\Models\Product;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Get all products.
     */
    public function getProducts()
    {
        $products = Product::paginate(25);

        return response()->json([
            'products' => API::repaginate($products),
        ], 200);
    }
}
