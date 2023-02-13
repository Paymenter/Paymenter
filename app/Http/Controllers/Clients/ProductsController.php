<?php

namespace App\Http\Controllers\Clients;

use App\Models\OrderProducts;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    public function index(OrderProducts $product)
    {
        if ($product->order()->get()->first()->client != Auth::user()->id) {
            return abort(404);
        }

        $link = ExtensionHelper::getLink($product);

        $product = $product->product()->get()->first();


        return view('clients.products.view', compact('product', 'link'));
    }
}
