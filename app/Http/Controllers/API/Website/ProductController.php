<?php
namespace App\Http\Controllers\API\Website;

use App\Classes\API;
use App\Http\Controllers\Controller;
use App\Models\Products;

class ProductController extends Controller
{
    /**
     * Get all products
     */
    public function getProducts() {
        $products = Products::paginate(25);

        return response()->json([
            'products' => API::repaginate($products)
        ], 200);
    }
}