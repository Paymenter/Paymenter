<?php
namespace App\Http\Controllers;

use App\Helpers\ExtensionHelper;
use App\Models\Orders;
use App\Models\Products;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;


class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $products = session('cart');
        return view('checkout.index', compact('products'));
    }

    public function checkout(Request $request)
    {
        $products = session('cart');
        $user = User::find(auth()->user()->id);
        $order = new Orders();
        $order->user_id = $user->id;
        $order->total = $request->total;
        $order->save();
        foreach ($products as $product) {
            $order->products()->attach($product['id'], ['quantity' => $product['quantity']]);
            $product = Products::find($product['id']);
            $product->stock = $product->stock - $product['quantity'];
            $product->save();
        }
        session()->forget('cart');
        return redirect()->route('checkout.success');
    }

    public function success()
    {
        return view('checkout.success');
    }

    public function cancel()
    {
        return view('checkout.cancel');
    }

    public function add(Request $request)
    {
        $product = Products::find($request->id);
            session()->put('cart', []);
        session()->push('cart', $product);
        return redirect()->route('checkout.index');
    }
}