<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Models\{Extension, Invoice, OrderProduct, OrderProductConfig, Order, Product, User, Coupon};

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $products = session('cart');
        $total = 0;
        $discount = 0;
        $couponId = session('coupon');
        if ($couponId) {
            $coupon = Coupon::where('id', $couponId)->first();
        } else {
            $coupon = null;
        }
        if ($products) {
            foreach ($products as $product) {
                $total += $product->price * $product->quantity;
                if ($coupon) {
                    if (!in_array($product->id, $coupon->products) && $coupon->type != 'all') {
                        $product->discount = 0;
                        continue;
                    }
                    if ($coupon->type == 'percent') {
                        $product->discount = $product->price * $coupon->value / 100;
                        $discount += $product->discount * $product->quantity;
                    } else {
                        $product->discount = $coupon->value;
                        $discount += $product->discount * $product->quantity;
                    }
                } else {
                    $product->discount = 0;
                }
            }
        }

        return view('checkout.index', compact('products', 'total', 'discount', 'coupon'));
    }

    public function add(Product $product)
    {
        if ($product->stock_enabled && $product->stock <= 0) {
            return redirect()->back()->with('error', 'Product is out of stock');
        }
        if (isset($product->server_id)) {
            $server = Extension::find($product->server_id);
            if ($server) {
                include_once base_path('app/Extensions/Servers/' . $server->name . '/index.php');
                $function = $server->name . '_getUserConfig';
                if (function_exists($function)) {
                    return redirect()->route('checkout.config', $product->id);
                }
            }
        }
        if ($product->prices()->get()->first()->type == 'recurring') {
            return redirect()->route('checkout.config', $product->id);
        }
        $product->quantity = 1;
        $cart = session()->get('cart');
        if (\Illuminate\Support\Arr::has($cart, $product->id)) {
            if ($product->stock_enabled && $product->stock <= $cart[$product->id]->quantity) {
                return redirect()->back()->with('error', 'Product is out of stock');
            }
            ++$cart[$product->id]->quantity;
            session()->put('cart', $cart);

            return redirect()->back()->with('success', 'Product added to cart successfully!');
        } else {
            $cart[$product->id] = $product;
            $product->price = $product->prices()->get()->first()->type == 'one-time' ? $product->prices()->get()->first()->monthly : 0;
            session()->put('cart', $cart);

            return redirect()->back()->with('success', 'Product added to cart successfully!');
        }
    }

    public function config(Request $request, Product $product)
    {
        $server = Extension::find($product->server_id);
        include_once base_path('app/Extensions/Servers/' . $server->name . '/index.php');
        $function = $server->name . '_getUserConfig';
        if (!function_exists($function) && $product->prices()->get()->first()->type != 'recurring') {
            return redirect()->back()->with('error', 'Config Not Found');
        }
        if (function_exists($function)) {
            $userConfig = json_decode(json_encode($function($product)));
        } else {
            $userConfig = array();
        }
        $prices = $product->prices()->get()->first();

        return view('checkout.config', compact('product', 'userConfig', 'prices'));
    }

    public function configPost(Request $request, Product $product)
    {
        $server = Extension::find($product->server_id);
        include_once base_path('app/Extensions/Servers/' . $server->name . '/index.php');
        $function = $server->name . '_getUserConfig';
        if (!function_exists($function) && $product->prices()->get()->first()->type != 'recurring') {
            return redirect()->back()->with('error', 'Config Not Found');
        }
        $prices = $product->prices()->get()->first();
        if (function_exists($function)) {
            $userConfig = json_decode(json_encode($function($product)));
            $config = [];
            foreach ($userConfig as $configItem) {
                if (!$request->input($configItem->name)) {
                    return redirect()->back()->with('error', $configItem->name . ' is required');
                }
                $config[$configItem->name] = $request->input($configItem->name);
            }
            $product->config = $config;
        }
        if ($prices->type == 'recurring') {
            $product->price = $product->prices()->get()->first()->{$request->input('billingcycle')} ? $product->prices()->get()->first()->{$request->input('billingcycle')} : $product->prices()->get()->first()->monthly;
        } else if ($prices->type == 'one-time') {
            $product->price = $product->prices()->get()->first()->monthly;
        } else {
            $product->price = 0;
        }
        $product->quantity = 1;
        $cart = session()->get('cart');
        if (\Illuminate\Support\Arr::has($cart, $product->id)) {
            ++$cart[$product->id]->quantity;
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
        $couponId = session('coupon');
        if ($couponId) {
            $coupon = Coupon::where('id', $couponId)->first();
        } else {
            $coupon = null;
        }
        $total = 0;
        foreach ($products as $product) {
            if ($product->stock_enabled && $product->stock <= 0) {
                return redirect()->back()->with('error', 'Product is out of stock');
            } elseif ($product->stock_enabled && $product->stock < $product->quantity) {
                return redirect()->back()->with('error', 'Product is out of stock');
            }
            $total += $product->price * $product->quantity;
        }

        $user = User::findOrFail(auth()->user()->id);
        $order = new Order();
        $order->client = $user->id;
        $order->expiry_date = date('Y-m-d H:i:s', strtotime('+1 month'));
        $order->status = 'pending';
        $order->coupon = session('coupon');
        $order->save();
        foreach ($products as $product) {
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $product->id;
            $orderProduct->quantity = $product->quantity;
            $orderProduct->price = $product->price;
            $orderProduct->save();
            if (isset($product->config)) {
                foreach ($product->config as $key => $value) {
                    $orderProductConfig = new OrderProductConfig();
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

        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->order_id = $order->id;
        if ($total == 0) {
            $invoice->status = 'paid';
        } else {
            $invoice->status = 'pending';
        }
        $invoice->save();

        session()->forget('cart');
        session()->forget('coupon');
        if (!config('settings::mail_disabled')) {
            try {
                \Illuminate\Support\Facades\Mail::to(auth()->user())->send(new \App\Mail\Orders\NewOrder($order));
            } catch (\Exception $e) {
            }
            if ($total != 0) {
                try {
                    \Illuminate\Support\Facades\Mail::to(auth()->user())->send(new \App\Mail\Invoices\NewInvoice($invoice));
                } catch (\Exception $e) {
                }
            }
        }
        if ($total != 0) {
            $total = $invoice->total;
            $products = [];
            foreach ($order->products()->get() as $product) {
                $iproduct = Product::where('id', $product->product_id)->first();
                $iproduct->quantity = $product['quantity'];
                $iproduct->price = $product['price'];
                if (isset($product['config'])) {
                    $iproduct->config = $product['config'];
                }
                if ($coupon) {
                    if (!in_array($iproduct->id, $coupon->products) && $coupon->type != 'all') {
                        $iproduct->discount = 0;
                    } else {
                        if ($coupon->type == 'percent') {
                            $iproduct->discount = $iproduct->price * $coupon->value / 100;
                        } else {
                            $iproduct->discount = $coupon->value;
                        }
                    }
                } else {
                    $iproduct->discount = 0;
                }
                $iproduct->price = $iproduct->price - $iproduct->discount;
                $products[] = $iproduct;
                $total += $iproduct->price * $iproduct->quantity;
            }

            if ($request->get('payment_method')) {
                $payment_method = $request->get('payment_method');
                $payment_method = ExtensionHelper::getPaymentMethod($payment_method, $total, $products, $invoice->id);
                if ($payment_method) {
                    return redirect($payment_method);
                } else {
                    return redirect()->back()->with('error', 'Payment method not found');
                }
            } else {
                return redirect()->route('clients.invoice.show', $invoice->id);
            }
        }

        return redirect()->route('clients.invoice.show', $invoice->id);
    }

    public function remove(Request $request, $product)
    {
        $cart = session()->get('cart');
        if (isset($cart[$product])) {
            unset($cart[$product]);
            session()->put('cart', $cart);
        }

        if (count($cart) == 0) {
            session()->forget('cart');
            session()->forget('coupon');
        }

        return redirect()->back()->with('success', 'Product removed successfully');
    }

    /*
     * Update product quantity in cart
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
        ]);
        $cart = session()->get('cart');
        if (isset($cart[$product->id])) {
            if ($product->stock_enabled && $product->stock < $request->quantity) {
                return redirect()->back()->with('error', 'Product is out of stock');
            }
            $cart[$product->id]->quantity = $request->quantity;
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Product updated successfully');
    }

    public function coupon(Request $request)
    {
        if ($request->get('remove')) {
            session()->forget('coupon');
            return redirect()->back()->with('success', 'Coupon removed successfully');
        }
        $request->validate([
            'coupon' => 'required',
        ]);
        $coupon = Coupon::where('code', $request->coupon)->first();
        if (!$coupon) {
            return redirect()->back()->with('error', 'Coupon not found');
        }
        if ($coupon->expiry_date) {
            if ($coupon->expiry_date < date('Y-m-d H:i:s')) {
                return redirect()->back()->with('error', 'Coupon expired');
            }
        }
        if ($coupon->max_uses) {
            if ($coupon->uses >= $coupon->max_uses) {
                return redirect()->back()->with('error', 'Coupon max uses reached');
            }
        }
        session()->put('coupon', $coupon->id);

        return redirect()->back()->with('success', 'Coupon applied successfully');
    }
}
