<?php

namespace App\Http\Controllers;

use App\Helpers\ExtensionHelper;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        return view('checkout.index');
    }

    public function config(Request $request, Product $product)
    {
        $server = $product->extension;
        $userConfig = ExtensionHelper::getUserConfig($product);
        if (! $server && $product->prices->type != 'recurring' && count($product->configurableGroups()) == 0 && empty($userConfig)) {
            return redirect()->back()->with('error', 'Config Not Found');
        }
        $cart = session()->get('cart', []);
        $key = array_search($product->id, array_column($cart, 'product_id'));
        if (! $product->allow_quantity && $cart && $key !== false) {
            return redirect()->route('checkout.index')->with('error', 'You already have this product in your shopping cart');
        }
        if ($product->stock_enabled && $product->stock <= 0) {
            return redirect()->route('checkout.index')->with('error', 'Product is out of stock');
        }

        if ($product->limit) {
            $orderProducts = 0;
            if (auth()->check() && auth()->user()->orderProducts) {
                $orderProducts = Auth::user()->orderProducts()->where('product_id', $product->id)->count();
                if ($orderProducts >= $product->limit) {
                    return redirect()->route('checkout.index')->with('error', 'Product limit reached');
                }
            }
            if ($key !== false && $cart[$key]['quantity'] + $orderProducts >= $product->limit) {
                return redirect()->route('checkout.index')->with('error', 'Product limit reached');
            }
        }

        return view('checkout.config', compact('product'));
    }
}
