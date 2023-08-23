<?php

namespace App\Http\Controllers\API\Website;

use App\Classes\API;
use App\Models\ProductPrice;
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
        $product_prices = ProductPrice::paginate(25);
        $counter = $product_prices->toArray();
        for($i=0;$i<count($counter['data']);$i++) {
            $product_prices[$i]['name'] = $products[$i]['name'];
            $product_prices[$i]['description'] = $products[$i]['description'];
            $product_prices[$i]['category_id'] = $products[$i]['category_id'];
            $product_prices[$i]['image'] = $products[$i]['image'];
            $product_prices[$i]['extension_id'] = $products[$i]['extension_id'];
            $product_prices[$i]['stock'] = $products[$i]['stock'];
            $product_prices[$i]['stock_enabled'] = $products[$i]['stock_enabled'];
            $product_prices[$i]['allow_quantity'] = $products[$i]['allow_quantity'];
            $product_prices[$i]['order'] = $products[$i]['order'];
        };
        return response()->json([
            'products' => API::repaginate($product_prices),
        ], 200);
    }
}
