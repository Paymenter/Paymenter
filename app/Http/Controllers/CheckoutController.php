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
        $total = 0;

        if($products){
            foreach ($products as $product) {
                $total += $product->price * $product->quantity;
            }
        }
        return view('checkout.index', compact('products', 'total'));	
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

        $product = json_decode(Products::find($request->id)->toJson());
        if(!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        $product->quantity = 1;
        $cart = session()->get('cart');
        if(\Illuminate\Support\Arr::has($cart, $product->id)) {
            $cart[$product->id]->quantity++;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Product added to cart successfully!');
        } else {
            $cart[$product->id] = $product;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Product added to cart successfully!');
        }




        //return redirect()->route('checkout.index');
    }

    public function pay(Request $request)
    {
        $products = session('cart');
        $total = 0;
        if(!$products) {
            return redirect()->back()->with('error', 'No products in cart');
        }
        if ($products) {
            foreach ($products as $product) {
                $total += $product->price * $product->quantity;
            }
        }
        $productsids = [];
        foreach ($products as $product) {
            $productsids[] = $product->id;
        }
        $user = User::find(auth()->user()->id);
        $order = new Orders();
        $order->client = $user->id;
        $order->total = $total;
        $order->products = $productsids;
        $order->expiry_date = date('Y-m-d H:i:s', strtotime('+1 month'));
        $order->status = 'pending';
        $order->save();

        if($request->get('payment_method')){
            $payment_method = $request->get('payment_method');
            $payment_method = ExtensionHelper::getPaymentMethod($payment_method, $total, $products, $order->id);
            if($payment_method){
                return redirect($payment_method);
            }else{
                return redirect()->back()->with('error', 'Payment method not found');
            }
        }else{
            return redirect()->back()->with('error', 'Payment method not found');
        }
    }
}