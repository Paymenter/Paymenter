<?php

namespace App\Http\Controllers;

use App\Helpers\ExtensionHelper;
use App\Models\{Orders, Products, User, Invoices, Extensions, OrderProducts, OrderProductsConfig};
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use stdClass;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $products = session('cart');
        $total = 0;

        if ($products) {
            foreach ($products as $product) {
                $total += $product->price * $product->quantity;
            }
        }
        return view('checkout.index', compact('products', 'total'));
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

        $product = json_decode(Products::findOrFail($request->id)->toJson());
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }
        // Get extension config
        if (isset($product->server_id)) {
            $server = Extensions::find($product->server_id);
            include_once base_path('app/Extensions/Servers/' . $server->name . '/index.php');
            $function = $server->name . '_getUserConfig';
            if (function_exists($function)) {
                return redirect()->route('checkout.config', $product->id);
            }
        }
        $product->quantity = 1;
        $cart = session()->get('cart');
        if (\Illuminate\Support\Arr::has($cart, $product->id)) {
            $cart[$product->id]->quantity++;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Product added to cart successfully!');
        } else {
            $cart[$product->id] = $product;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Product added to cart successfully!');
        }
    }

    public function config(Request $request, Products $id)
    {
        $product = $id;
        $server = Extensions::find($product->server_id);
        include_once(base_path('app/Extensions/Servers/' . $server->name . '/index.php'));
        $function = $server->name . '_getUserConfig';
        if (!function_exists($function)) {
            return redirect()->back()->with('error', 'Config Not Found');
        }
        $userConfig = json_decode(json_encode($function($product)));
        if (!isset($userConfig)) {
            return redirect()->route('checkout.index');
        }
        return view('checkout.config', compact('product', 'userConfig'));
    }

    public function configPost(Request $request, Products $id)
    {
        $product = $id;
        $server = Extensions::find($product->server_id);
        include_once(base_path('app/Extensions/Servers/' . $server->name . '/index.php'));
        $function = $server->name . '_getUserConfig';
        if (!function_exists($function)) {
            return redirect()->back()->with('error', 'Config Not Found');
        }
        $userConfig = json_decode(json_encode($function($product)));
        $config = [];
        foreach ($userConfig as $configItem) {
            $config[$configItem->name] = $request->input($configItem->name);
        }
        $product->config = $config;
        $product->quantity = 1;
        $cart = session()->get('cart');
        if (\Illuminate\Support\Arr::has($cart, $product->id)) {
            $cart[$product->id]->quantity++;
            session()->put('cart', $cart);
            return redirect()->route('checkout.index')->with('success', 'Product added to cart successfully!');
        } else {
            $cart[$product->id] = $product;
            session()->put('cart', $cart);
            return redirect()->route('checkout.index')->with('success', 'Product added to cart successfully!');
        }
    }

    public function pay(Request $request)
    {
        $products = session('cart');
        if (!$products) {
            return redirect()->back()->with('error', 'Cart is empty');
        }
        $total = 0;
        if ($products) {
            foreach ($products as $product) {
                $total += $product->price * $product->quantity;
            }
        }

        $user = User::find(auth()->user()->id);
        $order = new Orders();
        $order->client = $user->id;
        $order->total = $total;
        $order->expiry_date = date('Y-m-d H:i:s', strtotime('+1 month'));
        $order->status = 'pending';
        $order->save();
        foreach ($products as $product) {
            $orderProduct = new OrderProducts();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $product->id;
            $orderProduct->quantity = $product->quantity;
            $orderProduct->save();
            if (isset($product->config)) {
                foreach ($product->config as $key => $value) {
                    $orderProductConfig = new OrderProductsConfig();
                    $orderProductConfig->order_product_id = $orderProduct->id;
                    $orderProductConfig->key = $key;
                    $orderProductConfig->value = $value;
                    $orderProductConfig->save();
                }
            }
        }
        if ($total == 0) {
            $order->status = 'paid';
            $order->save();
            ExtensionHelper::createServer($order);
        }

        $invoice = new Invoices();
        $invoice->user_id = $user->id;
        $invoice->order_id = $order->id;
        if ($total == 0) {
            $invoice->status = 'paid';
        } else {
            $invoice->status = 'pending';
        }
        $invoice->save();

        session()->forget('cart');
        try {
            \Illuminate\Support\Facades\Mail::to(auth()->user())->send(new \App\Mail\Orders\NewOrder($order));
        } catch (\Exception $e) {
            error_log($e);
        }
        if ($total == 0) {
            try {
                \Illuminate\Support\Facades\Mail::to(auth()->user())->send(new \App\Mail\Invoices\NewInvoice($invoice));
            } catch (\Exception $e) {
                error_log($e);
            }
        }
        return redirect()->route('clients.invoice.show', ['id' => $invoice->id]);
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart');
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Product removed successfully');
    }

    /*
    * Update product quantity in cart
    */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1'
        ]);
        $cart = session()->get('cart');
        if (isset($cart[$id])) {
            $cart[$id]->quantity = $request->quantity;
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Product updated successfully');
    }
}
