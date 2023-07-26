<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Helpers\NotificationHelper;
use App\Models\{Extension, Invoice, OrderProduct, OrderProductConfig, Order, Product, User, Coupon, InvoiceItem};
use Illuminate\Support\Arr;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $products = session('cart');
        $total = 0;
        $totalSetup = 0;
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
                $totalSetup += $product->setup_fee * $product->quantity;
                if ($coupon) {
                    if (isset($coupon->products)) {
                        if (!in_array($product->id, $coupon->products) && !empty($coupon->products)) {
                            $product->discount = 0;
                            $product->discount_fee = 0;
                            continue;
                        } else {
                            if ($coupon->type == 'percent') {
                                $product->discount = $product->price * $coupon->value / 100;
                                $product->discount_fee = $product->setup_fee * $coupon->value / 100;
                            } else {
                                $product->discount = $coupon->value;
                                $product->discount_fee = $coupon->value;
                            }
                        }
                    } else {
                        if ($coupon->type == 'percent') {
                            $product->discount = $product->price * $coupon->value / 100;
                            $product->discount_fee = $product->setup_fee * $coupon->value / 100;
                        } else {
                            $product->discount = $coupon->value;
                            $product->discount_fee = $coupon->value;
                        }
                    }
                } else {
                    $product->discount = 0;
                    $product->discount_fee = 0;
                }
                $discount += ($product->discount + $product->discount_fee) * $product->quantity;
            }
        }
        $total += $totalSetup;
        return view('checkout.index', compact('products', 'total', 'discount', 'coupon', 'totalSetup'));
    }

    public function add(Product $product)
    {
        if ($product->stock_enabled && $product->stock <= 0) {
            return redirect()->back()->with('error', 'Product is out of stock');
        }
        if (isset($product->extension_id)) {
            $server = $product->extension;
            if ($server) {
                $module = "App\\Extensions\\Servers\\" . $server->name . "\\" . $server->name;
                if(class_exists($module)) {      
                    $module = new $module($server);
                    if (method_exists($module, 'getUserConfig')) {
                        return redirect()->route('checkout.config', $product->id);
                    }
                }
            }
        }
        if ($product->prices()->get()->first()->type == 'recurring' || count($product->configurableGroups()) > 0) {
            return redirect()->route('checkout.config', $product->id);
        }
        $product->quantity = 1;
        $cart = session()->get('cart');
        if (\Illuminate\Support\Arr::has($cart, $product->id)) {
            if ($product->stock_enabled && $product->stock <= $cart[$product->id]->quantity) {
                return redirect()->back()->with('error', 'Product is out of stock');
            }
            if ($product->quantity != 0) {
                ++$cart[$product->id]->quantity;
            }
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
        $server = $product->extension;
        if (!$server && $product->prices()->get()->first()->type != 'recurring' && count($product->configurableGroups()) == 0) {
            return redirect()->back()->with('error', 'Config Not Found');
        }
        if ($server) {
            include_once base_path('app/Extensions/Servers/' . $server->name . '/index.php');
            $function = $server->name . '_getUserConfig';
            if (!function_exists($function) && $product->prices()->get()->first()->type != 'recurring' && count($product->configurableGroups()) == 0) {
                return redirect()->back()->with('error', 'Config Not Found');
            }
            if (function_exists($function)) {
                $userConfig = json_decode(json_encode($function($product)));
            }
        }
        if (!isset($userConfig)) $userConfig = array();
        $prices = $product->prices()->get()->first();
        $customConfig = $product->configurableGroups();
        // If billing_cycle isn't set, set it to the lowest billing cycle available
        $billing_cycle = $request->input('billing_cycle') ?? 'monthly';
        if (!in_array($billing_cycle, ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially'])) {
            $billing_cycle = 'monthly';
        }
        if ($billing_cycle == 'monthly' && !$prices->monthly) {
            $billing_cycle = 'quarterly';
        }
        if ($billing_cycle == 'quarterly' && !$prices->quarterly) {
            $billing_cycle = 'semi_annually';
        }
        if ($billing_cycle == 'semi_annually' && !$prices->semi_annually) {
            $billing_cycle = 'annually';
        }
        if ($billing_cycle == 'annually' && !$prices->annually) {
            $billing_cycle = 'biennially';
        }
        if ($billing_cycle == 'biennially' && !$prices->biennially) {
            $billing_cycle = 'triennially';
        }
        if ($billing_cycle == 'triennially' && !$prices->triennially) {
            $billing_cycle = 'monthly';
        }

        return view('checkout.config', compact('product', 'userConfig', 'prices', 'customConfig', 'billing_cycle'));
    }

    public function configPost(Request $request, Product $product)
    {
        $server = $product->extension;
        $prices = $product->prices()->get()->first();
        if (!$server && $prices->type != 'recurring' && count($product->configurableGroups()) == 0) {
            return redirect()->back()->with('error', 'Config Not Found');
        }
        if ($server) {
            include_once base_path('app/Extensions/Servers/' . $server->name . '/index.php');
            $function = $server->name . '_getUserConfig';
            if (!function_exists($function) && $prices->type != 'recurring' && count($product->configurableGroups()) == 0) {
                return redirect()->back()->with('error', 'Config Not Found');
            }
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
        }
        if ($prices->type == 'recurring') {
            $product->price = $product->prices()->get()->first()->{$request->input('billing_cycle')} ?? $product->prices()->get()->first()->monthly;
            $product->billing_cycle = $request->input('billing_cycle');
            $product->setup_fee = $product->prices()->get()->first()->{$request->input('billing_cycle') . '_setup'} ?? 0;
        } else if ($prices->type == 'one-time') {
            $product->price = $product->prices()->get()->first()->monthly;
        } else {
            $product->price = 0;
        }
        $product->quantity = 1;
        $configItems = [];
        $resetBillingCycle = false;
        if (!$product->billing_cycle) {
            $resetBillingCycle = true;
            $product->billing_cycle = 'monthly';
        }
        foreach ($product->configurableGroups() as $config) {
            $configItemsGet = $config->configurableOptions()->get();
            foreach ($configItemsGet as $configItem) {
                if ($configItem->hidden) continue;
                if (!$request->has($configItem->id)) {
                    return redirect()->back()->with('error', $configItem->name . ' is required');
                }
                $configItems[$configItem->id] = $request->input($configItem->id);
                $configItemInput = $configItem->configurableOptionInputs()->get();
                foreach ($configItemInput as $configItemInput) {
                    if ($configItem->type == 'quantity') {
                        if (!$request->input($configItem->id)) continue;
                    }
                    if ($configItemInput->id != $request->input($configItem->id) && ($configItem->type == 'select' || $configItem->type == 'radio' || $configItem->type == 'slider')) continue;
                    $configItemPrice = $configItemInput->configurableOptionInputPrice()->get()->first();
                    if ($configItemPrice) {
                        if ($configItem->type == 'quantity') {
                            $product->price += $configItemPrice->{$product->billing_cycle} * $request->input($configItem->id);
                        } else {
                            $product->price += $configItemPrice->{$product->billing_cycle};
                        }
                        $product->setup_fee += $configItemPrice->{$product->billing_cycle . '_setup'};
                    }
                }
            }
        }
        if ($resetBillingCycle) {
            $product->billing_cycle = null;
        }
        $product->configurableOptions = $configItems;
        $cart = session()->get('cart');
        if (\Illuminate\Support\Arr::has($cart, $product->id)) {
            if ($product->quantity != 0) {
                ++$cart[$product->id]->quantity;
            } else {
                $cart[$product->id] = $product;
            }
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
        $cart = session('cart');
        if (!$cart) {
            return redirect()->back()->with('error', 'Cart is empty');
        }
        if ($request->has('tos') && config('settings::tos') == 1) {
            $tos = $request->input('tos');
            if ($tos == 'on') {
                $tos = true;
            } else {
                $tos = false;
            }
            if (!$tos) {
                return redirect()->back()->with('error', 'You must agree to the terms of service');
            }
        } else if (config('settings::tos') == 1) {
            return redirect()->back()->with('error', 'You must agree to the terms of service');
        }
        $couponId = session('coupon');
        $coupon;
        if ($couponId) {
            $coupon = Coupon::where('id', $couponId)->first();
        } else {
            $coupon = null;
        }
        $total = 0;
        $products = [];
        foreach ($cart as $product) {
            if ($product->stock_enabled && $product->stock <= 0) {
                return redirect()->back()->with('error', 'Product is out of stock');
            } elseif ($product->stock_enabled && $product->stock < $product->quantity) {
                return redirect()->back()->with('error', 'Product is out of stock');
            }
            if ($coupon) {
                if (isset($coupon->products)) {
                    if (!in_array($product->id, $coupon->products) && !empty($coupon->products)) {
                        $product->discount = 0;
                        continue;
                    } else {
                        if ($coupon->type == 'percent') {
                            $product->discount = $product->price * $coupon->value / 100;
                            $product->discount_fee = $product->setup_fee * $coupon->value / 100;
                        } else {
                            $product->discount = $coupon->value;
                            $product->discount_fee = $coupon->value;
                        }
                    }
                } else {
                    if ($coupon->type == 'percent') {
                        $product->discount = $product->price * $coupon->value / 100;
                        $product->discount_fee = $product->setup_fee * $coupon->value / 100;
                    } else {
                        $product->discount = $coupon->value;
                        $product->discount_fee = $coupon->value;
                    }
                }
            } else {
                $product->discount = 0;
                $product->discount_fee = 0;
            }
            if ($product->setup_fee) {
                $total += ($product->setup_fee + $product->price) * $product->quantity - $product->discount - $product->discount_fee;
            } else {
                $total += $product->price * $product->quantity - $product->discount;
            }
            $products[] = $product;
        }

        $user = User::findOrFail(auth()->user()->id);
        $order = new Order();
        $order->user()->associate($user);
        $order->coupon = $coupon->id ?? null;
        $order->save();

        $invoice = new Invoice();
        $invoice->user()->associate($user);
        if ($total == 0) {
            $invoice->status = 'paid';
        } else {
            $invoice->status = 'pending';
        }
        $invoice->order()->associate($order);
        $invoice->save();
        foreach ($products as $product) {
            if ($product->allow_quantity == 1)
                for (
                    $i = 0;
                    $i < $product->quantity;
                    ++$i
                ) {
                    $this->createOrderProduct($order, $product, $invoice, false);
                }
            else if ($product->allow_quantity == 2)
                $this->createOrderProduct($order, $product, $invoice);
            else
                $this->createOrderProduct($order, $product, $invoice);
            if ($product->setup_fee > 0) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->description = $product->name . ' Setup Fee';
                $invoiceItem->total = ($product->setup_fee - $product->discount_fee) * $product->quantity;
                $invoiceItem->save();
            }
        }

        session()->forget('cart');
        session()->forget('coupon');
        if (!config('settings::mail_disabled')) {
            NotificationHelper::sendNewOrderNotification($order, auth()->user());
            if ($total != 0) {
                NotificationHelper::sendNewInvoiceNotification($invoice, auth()->user());
            }
        }
        foreach ($order->products()->get() as $product) {
            $iproduct = Product::where('id', $product->product_id)->first();
            if ($iproduct->stock_enabled) {
                $iproduct->stock = $iproduct->stock - $product->quantity;
                $iproduct->save();
            }
        }
        if ($coupon) {
            $coupon->uses = $coupon->uses + 1;
            $coupon->save();
        }
        if ($total != 0) {
            $products = $invoice->getItemsWithProducts()->products;
            $total = $invoice->getItemsWithProducts()->total;
            if ($total == 0) {
                $invoice->status = 'paid';
                $invoice->save();

                return redirect()->route('clients.invoice.show', $invoice->id);
            }

            if ($request->get('payment_method')) {
                $payment_method = $request->get('payment_method');
                if ($payment_method == 'credits') {
                    $user = User::where('id', auth()->user()->id)->first();
                    if ($user->credits < $total) {
                        return redirect()->route('clients.invoice.show', $invoice->id)->with('error', 'You do not have enough credits');
                    }
                    $user->credits = $user->credits - $total;
                    $user->save();
                    ExtensionHelper::paymentDone($invoice->id);
                    return redirect()->route('clients.invoice.show', $invoice->id)->with('success', 'Payment done');
                }
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

        return redirect()->route('clients.home')->with('success', 'Order created successfully');
    }

    private function createOrderProduct(Order $order, Product $product, Invoice $invoice, $setQuantity = true)
    {
        $orderProduct = new OrderProduct();
        $orderProduct->order_id = $order->id;
        $orderProduct->product_id = $product->id;
        $orderProduct->quantity = $product->quantity;
        $orderProduct->price = $product->price;
        if ($product->billing_cycle) {
            $orderProduct->billing_cycle = $product->billing_cycle;
            if ($product->billing_cycle == 'monthly') {
                $orderProduct->expiry_date = date('Y-m-d H:i:s', strtotime('+1 month'));
            } elseif ($product->billing_cycle == 'quarterly') {
                $orderProduct->expiry_date = date('Y-m-d H:i:s', strtotime('+3 months'));
            } elseif ($product->billing_cycle == 'semi_annually') {
                $orderProduct->expiry_date = date('Y-m-d H:i:s', strtotime('+6 months'));
            } elseif ($product->billing_cycle == 'annually') {
                $orderProduct->expiry_date = date('Y-m-d H:i:s', strtotime('+1 year'));
            } elseif ($product->billing_cycle == 'biennially') {
                $orderProduct->expiry_date = date('Y-m-d H:i:s', strtotime('+2 years'));
            } elseif ($product->billing_cycle == 'triennially') {
                $orderProduct->expiry_date = date('Y-m-d H:i:s', strtotime('+3 years'));
            }
            $orderProduct->save();
        }

        if ($setQuantity) $orderProduct->quantity = $product->quantity ?? 1;
        else $orderProduct->quantity = 1;
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
        if (isset($product->configurableOptions)) {
            foreach ($product->configurableOptions as $key => $value) {
                $orderProductConfig = new OrderProductConfig();
                $orderProductConfig->order_product_id = $orderProduct->id;
                $orderProductConfig->key = $key;
                $orderProductConfig->value = $value;
                $orderProductConfig->is_configurable_option = true;
                $orderProductConfig->save();
            }
        }
        if ($product->price == 0 || $product->price - $product->discount == 0) {
            $orderProduct->status = 'paid';
            $orderProduct->save();
            ExtensionHelper::createServer($orderProduct);
            return;
        } else {
            $orderProduct->status = 'pending';
            $orderProduct->save();
        }
        $invoiceProduct = new InvoiceItem();
        $invoiceProduct->invoice_id = $invoice->id;
        $invoiceProduct->product_id = $orderProduct->id;
        $invoiceProduct->total = $orderProduct->price * $orderProduct->quantity;
        $description = $orderProduct->billing_cycle ? '(' . now()->format('Y-m-d') . ' - ' . date('Y-m-d', strtotime($orderProduct->expiry_date)) . ')' : '';
        $invoiceProduct->description = $product->name . ' ' . $description;
        $invoiceProduct->save();
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
            if ($cart[$product->id]->quantity != 0) {
                $cart[$product->id]->quantity = $request->quantity;
                session()->put('cart', $cart);
            } else {
                session()->put('cart', $cart);
            }
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
