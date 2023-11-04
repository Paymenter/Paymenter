<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders.index');
    }

    public function show(Order $order)
    {
        $products = [];
        // Loop through products
        foreach ($order->products as $product) {
            $link = ExtensionHelper::getLink($product);
            $product->link = $link;
            $products[] = $product;
        }

        return view('admin.orders.show', compact('order', 'products'));
    }

    public function destroyProduct(Order $order, OrderProduct $product)
    {
        ExtensionHelper::terminateServer($product);
        $product->delete();

        return redirect()->route('admin.orders.show', $order)->with('success', 'Product deleted');

    }

    public function changeProduct(Order $order, OrderProduct $product, Request $request)
    {
        $request->validate([
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);

        $product->price = $request->input('price');
        $product->quantity = $request->input('quantity');
        $product->expiry_date = $request->input('expiry_date');
        $product->save();

        return redirect()->route('admin.orders.show', $order);
    }


    public function destroy(Order $order)
    {
        foreach ($order->products()->get() as $product) {
            ExtensionHelper::terminateServer($product);
        }
        $order->products()->delete();
        $order->invoices()->delete();
        $order->delete();

        return redirect()->route('admin.orders')->with('success', 'Order deleted');
    }

    public function suspend(Order $order)
    {
        foreach ($order->products()->get() as $product) {
            ExtensionHelper::suspendServer($product);
            $product->status = 'suspended';
            $product->save();
        }


        return redirect()->route('admin.orders.show', $order);
    }

    public function unsuspend(Order $order)
    {
        foreach ($order->products()->get() as $product) {
            ExtensionHelper::unsuspendServer($product);
            $product->status = 'paid';
            $product->save();
        }

        return redirect()->route('admin.orders.show', $order);
    }

    public function create(Order $order)
    {
        foreach ($order->products()->get() as $product) {
            ExtensionHelper::createServer($product);
            $product->status = 'paid';
            $product->save();
        }

        return redirect()->route('admin.orders.show', $order);
    }

    public function paid(Order $order)
    {
        foreach ($order->products()->get() as $product) {
            ExtensionHelper::unsuspendServer($product);
            $product->status = 'paid';
            $product->save();
        }

        foreach ($order->invoices()->get() as $invoice) {
            $invoice->status = 'paid';
            $invoice->save();
        }

        return redirect()->route('admin.orders.show', $order);
    }
}
