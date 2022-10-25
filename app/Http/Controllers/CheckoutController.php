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
                error_log($product);
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

        $product = Products::find($request->id);
        if(!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }
        if(!session()->has('cart')) {
            session()->put('cart', []);
        }
        if(session() && session()->has('cart')) {
            $cart = session()->get('cart');
            if(array_key_exists($product->id, $cart)) {
                $cart[$product->id]->quantity = $cart[$product->id]->quantity  + 1;
                session()->push('cart', $cart);
            } else {
                $product->quantity = 1;
                session()->push('cart', $product);
            }
        }

        return redirect()->route('checkout.index');
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
        }
    }
}