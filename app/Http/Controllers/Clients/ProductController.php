<?php

namespace App\Http\Controllers\Clients;

use App\Models\OrderProduct;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(OrderProduct $product)
    {
        if(!$product->order()->exists()) {
            return abort(404, 'Order not found');
        }
        if ($product->order()->get()->first()->client != Auth::user()->id) {
            return abort(404, 'Order not found');
        }

        $link = ExtensionHelper::getLink($product);
        $views = ExtensionHelper::getCustomPages($product);
        $orderProduct = $product;
        $product = $product->product()->get()->first();

        return view('clients.products.view', compact('product', 'link', 'orderProduct', 'views'));
    }

    public function show(OrderProduct $product, string $link)
    {
        if(!$product->order()->exists()) {
            return abort(404, 'Order not found');
        }
        if ($product->order()->get()->first()->client != Auth::user()->id) {
            return abort(404, 'Order not found');
        }
        $extensionLink = $link;
        $link = ExtensionHelper::getLink($product, $link);
        $views = ExtensionHelper::getCustomPages($product);
        $orderProduct = $product;
        $product = $product->product()->get()->first();

        return view('clients.products.view', compact('product', 'link', 'orderProduct', 'views', 'extensionLink'));
    }
}
