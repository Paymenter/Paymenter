<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();

        return view('admin.orders.index', compact('orders'));
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

    public function changeProduct(Order $order, OrderProduct $product, Request $request)
    {
        $request->validate([
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        $product->price = $request->input('price');
        $product->quantity = $request->input('quantity');
        $product->save();

        return redirect()->route('admin.orders.show', $order);
    }


    public function destroy(Order $order)
    {
        if ($order->status == 'paid' || $order->status == 'suspended') {
            ExtensionHelper::terminateServer($order);
        }
        $order->delete();

        return redirect()->route('admin.orders')->with('success', 'Order deleted');
    }

    public function suspend(Order $order)
    {
        $order->status = 'suspended';
        $order->save();
        ExtensionHelper::suspendServer($order);

        return redirect()->route('admin.orders.show', $order);
    }

    public function unsuspend(Order $order)
    {
        $order->status = 'paid';
        $order->save();
        ExtensionHelper::unsuspendServer($order);

        return redirect()->route('admin.orders.show', $order);
    }

    public function create(Order $order)
    {
        $order->status = 'paid';
        $order->save();
        ExtensionHelper::createServer($order);

        return redirect()->route('admin.orders.show', $order);
    }

    public function paid(Order $order)
    {
        $order->status = 'paid';
        $order->save();
        ExtensionHelper::createServer($order);

        foreach($order->invoices()->get() as $invoice) {
            $invoice->status = 'paid';
            $invoice->save();
        }

        return redirect()->route('admin.orders.show', $order);
    }
}
