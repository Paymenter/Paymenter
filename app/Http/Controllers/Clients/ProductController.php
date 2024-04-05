<?php

namespace App\Http\Controllers\Clients;

use App\Models\OrderProduct;
use App\Helpers\ExtensionHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Jobs\Servers\TerminateServer;
use App\Jobs\Servers\UpgradeServer;
use App\Models\OrderProductUpgrade;
use App\Models\Product;
use App\Models\TaxRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(OrderProduct $product)
    {
        $product->load(['product', 'order', 'order.user']);
        if ($product->order->user != Auth::user()) {
            return abort(404, 'Order not found');
        }
        $link = ExtensionHelper::getLink($product);
        $views = ExtensionHelper::getCustomPages($product);
        $orderProduct = $product;
        $product = $product->product;

        return view('clients.products.view', compact('product', 'link', 'orderProduct', 'views'));
    }

    public function show(OrderProduct $product, string $link)
    {
        if ($product->order->user != Auth::user()) {
            return abort(404, 'Order not found');
        }
        $extensionLink = $link;
        $link = ExtensionHelper::getLink($product, $link);
        $views = ExtensionHelper::getCustomPages($product);
        // Check if array has link
        if (isset($views['pages'])) {
            if (in_array($extensionLink, array_column($views['pages'], 'url'))) {
                $orderProduct = $product;
                $product = $product->product()->get()->first();

                return view('clients.products.view', compact('product', 'link', 'orderProduct', 'views', 'extensionLink'));
            }
        }
        return abort(404, 'Page not found');
    }

    public function cancel(OrderProduct $product, Request $request)
    {
        if ($product->cancellation) {
            return redirect()->back()->with('error', 'This product is already cancelled.');
        }
        if ($request->cancellation_type == 'immediate') {
            TerminateServer::dispatch($product);
        }

        $product->cancellation()->create([
            'reason' => $request->cancellation_reason,
        ]);

        return redirect()->back()->with('success', 'Product cancelled successfully.');
    }

    public function upgrade(OrderProduct $product)
    {
        $product->load(['product', 'order', 'order.user']);
        if ($product->order->user != Auth::user()) {
            return abort(404, 'Order not found');
        }
        $orderProduct = $product;
        if (!$orderProduct->upgradable) {
            return redirect()->back()->with('error', 'No upgrades available.');
        }
        $product = $product->product;

        return view('clients.products.upgrade', compact('product', 'orderProduct'));
    }

    public function upgradeProduct(OrderProduct $orderProduct, Product $product)
    {
        $orderProduct->load(['product', 'order', 'order.user']);

        if ($orderProduct->order->user != Auth::user() || !$orderProduct->upgradable) {
            return abort(404, 'Order not found');
        }

        if (!$orderProduct->product->upgrades()->where('upgrade_product_id', $product->id)->exists()) {
            return abort(404, 'Product not found');
        }

        if (!$product->prices->{$orderProduct->billing_cycle}) {
            // Couldn't find the same price, exit
            return abort(404, 'Product not found');
        }

        // Calculate amount for today
        $amount = $this->calculateAmount($product, $orderProduct);

        $tax = $this->calculateTax($amount);


        return view('clients.products.upgrade-product', compact('product', 'orderProduct', 'amount', 'tax'));
    }

    public function upgradeProductPost(OrderProduct $orderProduct, Product $product, Request $request)
    {
        $orderProduct->load(['product', 'order', 'order.user']);

        if ($orderProduct->order->user != Auth::user() || !$orderProduct->upgradable) {
            return abort(404, 'Order not found');
        }

        if (!$orderProduct->product->upgrades()->where('upgrade_product_id', $product->id)->exists()) {
            return abort(404, 'Product not found');
        }

        if (!$product->prices->{$orderProduct->billing_cycle}) {
            // Couldn't find the same price, exit
            return abort(404, 'Product not found');
        }

        // Calculate amount for today
        $amount = $this->calculateAmount($product, $orderProduct);

        if ($amount <= 0) {
            $user = Auth::user();
            $user->credits += $amount * -1;
            $user->save();

            $orderProduct->product_id = $product->id;
            $orderProduct->price -= $orderProduct->product->price($orderProduct->billing_cycle);
            $orderProduct->price += $product->price($orderProduct->billing_cycle);
            $orderProduct->save();

            UpgradeServer::dispatch($orderProduct);

            return redirect()->route('clients.active-products.show', $orderProduct)->with('success', 'Product upgraded successfully.');
        }
        $orderProductUpgrade = new OrderProductUpgrade();
        $orderProductUpgrade->order_product_id = $orderProduct->id;
        $orderProductUpgrade->product_id = $product->id;

        // Make invoice
        $invoice = new \App\Models\Invoice();
        $invoice->order_id = $orderProduct->order->id;
        $invoice->status = 'pending';
        $invoice->user_id = $orderProduct->order->user_id;
        $invoice->saveQuietly();

        $invoiceItem = new \App\Models\InvoiceItem();
        $invoiceItem->invoice_id = $invoice->id;
        $invoiceItem->product_id = $orderProduct->id;
        $invoiceItem->description = 'Upgrade ' . $orderProduct->product->name . ' to ' . $product->name;
        $invoiceItem->total = $amount;
        $invoiceItem->save();

        $orderProductUpgrade->invoice_id = $invoice->id;
        $orderProductUpgrade->save();

        NotificationHelper::sendNewInvoiceNotification($invoice, $orderProduct->order->user);

        event(new \App\Events\Invoice\InvoiceCreated($invoice));

        return (new InvoiceController)->pay($request, $invoice);
    }

    private function calculateTax($amount)
    {
        if (!config('settings::tax_enabled')) {
            return [
                'amount' => 0,
                'tax' => null,
            ];
        }
        if (!auth()->check()) {
            $tax = TaxRate::where('country', 'all')->first();
        } else {
            $tax = TaxRate::whereIn('country', [auth()->user()->country, 'all'])->get()->sortBy(function ($taxRate) {
                return $taxRate->country == 'all';
            })->first();
        }
        if (!$tax) {
            return [
                'amount' => 0,
                'tax' => null,
            ];
        }

        return [
            'amount' => $amount * $tax->rate / 100,
            'tax' => $tax,
        ];
    }


    private $cycleToDays = [
        'monthly' => 30,
        'quarterly' => 90,
        'semi-annually' => 180,
        'annually' => 365,
        'biennially' => 730,
        'triennially' => 1095,
    ];

    private function calculateAmount($product, $orderProduct)
    {
        $amount =  ($product->price($orderProduct->billing_cycle) - $orderProduct->product->price($orderProduct->billing_cycle)) / $this->cycleToDays[$orderProduct->billing_cycle] * $orderProduct->expiry_date->diffInDays();

        return $amount;
    }
}
