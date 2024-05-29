<?php

namespace App\Livewire\Checkout;

use App\Helpers\ExtensionHelper;
use App\Helpers\NotificationHelper;
use App\Jobs\Servers\CreateServer;
use App\Models\Coupon;
use App\Models\Extension;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductConfig;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Index extends Component
{
    #[Rule('exists:coupons,code')]
    public $couponCode;

    public $coupon;

    #[Rule('required|accepted')]
    public $tos;

    public $payment_method;

    public $total;

    public $totalSetup;

    public $discount;

    public $gateways;

    protected $casts = [
        'products' => 'collection',
    ];

    public function mount()
    {
        $this->updateCart();
    }

    #[On('updateCart')]
    public function updateCart()
    {
        $couponId = session('coupon');
        if ($couponId) {
            $coupon = Coupon::where('id', $couponId)->first();
        } else {
            $coupon = null;
        }
        $this->coupon = $coupon;

        $this->products;

        $this->gateways = ExtensionHelper::getAvailableGateways($this->total, $this->products);

        if(!isset($this->payment_method) || !in_array($this->payment_method, $this->gateways->pluck('id')->toArray())) $this->payment_method = $this->gateways->first()->id ?? null;
    }

    #[Computed()]
    public function products()
    {
        $cart = session('cart');
        $total = 0;
        $totalSetup = 0;
        $discount = 0;
        $products = [];
        if ($cart) {
            foreach ($cart as $key => $product2) {
                $product = Product::where('id', $product2['product_id'])->first();
                $product->config = $product2['config'] ?? [];
                $product->configurableOptions = $product2['configurableOptions'] ?? [];
                $product->quantity = $product2['quantity'];
                $product->price = $product2['price'] ?? 0;
                $product->billing_cycle = $product2['billing_cycle'] ?? null;
                $product->setup_fee = $product2['setup_fee'] ?? 0;
                $total += $product->price * $product->quantity;
                $totalSetup += $product->setup_fee * $product->quantity;
                if ($this->coupon) {
                    if (isset($this->coupon->products)) {
                        if (!in_array($product->id, $this->coupon->products) && !empty($this->coupon->products)) {
                            $product->discount = 0;
                            $product->discount_fee = 0;
                        } else {
                            if ($this->coupon->type == 'percent') {
                                $product->discount = $product->price * $this->coupon->value / 100;
                                $product->discount_fee = $product->setup_fee * $this->coupon->value / 100;
                            } else {
                                $product->discount = $this->coupon->value;
                                $product->discount_fee = $this->coupon->value;
                            }
                        }
                    } else {
                        if ($this->coupon->type == 'percent') {
                            $product->discount = $product->price * $this->coupon->value / 100;
                            $product->discount_fee = $product->setup_fee * $this->coupon->value / 100;
                        } else {
                            $product->discount = $this->coupon->value;
                            $product->discount_fee = $this->coupon->value;
                        }
                    }
                } else {
                    $product->discount = 0;
                    $product->discount_fee = 0;
                }
                if ($product->discount > $product->price) {
                    $product->discount = $product->price;
                }
                if ($product->discount_fee > $product->setup_fee) {
                    $product->discount_fee = $product->setup_fee;
                }
                $price = $this->calculateTax($product->price * $product->quantity - $product->discount);
                $setupFee = $this->calculateTax($product->setup_fee * $product->quantity - $product->discount_fee);
                if (config('settings::tax_type') == 'exclusive') {
                    $total += $price;
                    $totalSetup += $setupFee;
                }
                $product->tax = $price;
                $product->taxSetup = $setupFee;
                $this->tax->amount ?? $this->tax->amount += $price + $setupFee;
                $discount += ($product->discount + $product->discount_fee) * $product->quantity;

                $products[$key] = $product;
            }
        }

        $this->total = $total + $totalSetup;
        $this->totalSetup = $totalSetup;
        $this->discount = $discount;

        return $products;
    }

    public $tax;

    public function calculateTax($amount)
    {
        if (!config('settings::tax_enabled')) {
            if(!isset($this->tax)) $this->tax = new TaxRate();
            return 0;
        }
        if (!$this->tax) {
            if (!auth()->check()) {
                $this->tax = TaxRate::where('country', 'all')->first();
            } else {
                $this->tax = TaxRate::whereIn('country', [auth()->user()->country, 'all'])->get()->sortBy(function ($taxRate) {
                    return $taxRate->country == 'all';
                })->first();
            }
            $this->tax = $this->tax ?? new TaxRate();
        }
        return $amount * ($this->tax->rate / 100);
    }


    public function validateCoupon()
    {
        $this->validateOnly('couponCode');
        if ($this->couponCode) {
            $coupon = Coupon::where('code', $this->couponCode)->first();
            if ($coupon) {
                session()->put('coupon', $coupon->id);
                $this->updateCart();
            }
        }
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        $this->coupon = null;
        $this->mount();
    }

    public function updateQuantity($product, $value)
    {
        $cart = session()->get('cart');
        $key = array_search($product, array_column($cart, 'product_id'));
        $cart[$key]['quantity'] = $value;
        session()->put('cart', $cart);
        $this->updateCart();
    }

    public function removeProduct($key)
    {
        $cart = session()->get('cart');
        unset($cart[$key]);
        session()->put('cart', $cart);
        $this->updateCart();
    }

    public function pay()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        if (config('settings::requiredClientDetails_address') && !auth()->user()->address) return redirect()->route('clients.profile')->with(['error' => 'Please define your address.']);
        if (config('settings::requiredClientDetails_city') && !auth()->user()->city) return redirect()->route('clients.profile')->with(['error' => 'Please define your city.']);
        if (config('settings::requiredClientDetails_country') && !auth()->user()->country) return redirect()->route('clients.profile')->with(['error' => 'Please define your country.']);
        if (config('settings::requiredClientDetails_phone') && !auth()->user()->phone) return redirect()->route('clients.profile')->with(['error' => 'Please define your phone number.']);

        if (config('settings::tos') == 1) {
            $this->validateOnly('tos', [
                'tos' => 'required|accepted',
            ], [
                'tos.required' => 'You must accept the terms of service',
                'tos.accepted' => 'You must accept the terms of service',
            ]);
        }
        $coupon = $this->coupon;

        $total = 0;
        $products = [];
        foreach ($this->products as $product) {
            if ($product->stock_enabled && $product->stock <= 0) {
                return $this->addError('product.' . $product->id, 'Out of stock');
            } elseif ($product->stock_enabled && $product->stock < $product->quantity) {
                return $this->addError('product.' . $product->id, 'Only ' . $product->stock . ' left in stock');
            }
            if ($product->limit) {
                $orderProducts = 0;
                if (auth()->check() && auth()->user()->orderProducts) {
                    $orderProducts = Auth::user()->orderProducts()->where('product_id', $product->id)->count();
                }
                if ($orderProducts + $product->quantity > $product->limit) {
                    $cart = session()->get('cart');
                    if (isset($cart[$product->id])) {
                        unset($cart[$product->id]);
                        session()->put('cart', $cart);
                    }
                    return $this->addError('product.' . $product->id, 'You can only order ' . $product->limit . ' of this product');
                }
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
            if ($product->discount > $product->price) {
                $product->discount = $product->price;
            }
            if ($product->discount_fee > $product->setup_fee) {
                $product->discount_fee = $product->setup_fee;
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
        $order->coupon_id = $coupon->id ?? null;
        $order->save();

        $invoice = new Invoice();
        $invoice->user()->associate($user);
        if ($total == 0) {
            $invoice->status = 'paid';
        } else {
            $invoice->status = 'pending';
        }
        $invoice->order()->associate($order);
        // As the ->total() isn't available for events yet, we trigger it manually
        $invoice->saveQuietly();
        foreach ($products as $product) {
            if ($product->allow_quantity == 1)
                for (
                    $i = 0;
                    $i < $product->quantity;
                    ++$i
                ) {
                    $orderProductCreated = $this->createOrderProduct($order, $product, $invoice, false);
                }
            else if ($product->allow_quantity == 2)
                $orderProductCreated = $this->createOrderProduct($order, $product, $invoice);
            else
                $orderProductCreated = $this->createOrderProduct($order, $product, $invoice);
            if ($product->setup_fee > 0) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->description = $product->name . ' Setup Fee';
                $invoiceItem->product_id = $orderProductCreated->id;
                $invoiceItem->total = $product->setup_fee * $product->quantity;
                $invoiceItem->save();
            }
        }
        // Trigger event
        event(new \App\Events\Invoice\InvoiceCreated($invoice));

        session()->forget('cart');
        session()->forget('coupon');
        NotificationHelper::sendNewOrderNotification($order, auth()->user());
        if ($total != 0) {
            NotificationHelper::sendNewInvoiceNotification($invoice, auth()->user());
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
            $invoice = Invoice::where('id', $invoice->id)->first();
            $invoiceTotalAndProducts = $invoice->getItemsWithProducts();
            $products = $invoiceTotalAndProducts->products;
            $total = $invoiceTotalAndProducts->total;

            if ($invoiceTotalAndProducts->tax->amount > 0 && config('settings::tax_type') == 'exclusive') {
                foreach ($products as $product) {
                    $product->price = $product->price + ($product->price * $invoiceTotalAndProducts->tax->rate / 100);
                }
            }

            if ($total == 0) {
                $invoice->status = 'paid';
                $invoice->save();

                return redirect()->route('clients.invoice.show', $invoice->id);
            }

            if ($this->payment_method) {
                if ($this->payment_method == 'credits') {
                    $user = User::where('id', auth()->user()->id)->first();
                    if ($user->credits < $total) {
                        return redirect()->route('clients.invoice.show', $invoice->id)->with('error', 'You do not have enough credits');
                    }
                    $user->credits = $user->credits - $total;
                    $user->save();
                    ExtensionHelper::paymentDone($invoice->id);
                    return redirect()->route('clients.invoice.show', $invoice->id)->with('success', 'Payment done');
                }
                $payment_method = ExtensionHelper::getPaymentMethod($this->payment_method, $total, $products, $invoice->id);
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
            CreateServer::dispatch($orderProduct);
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

        return $orderProduct;
    }


    public function render()
    {
        return view('livewire.checkout.index');
    }
}
