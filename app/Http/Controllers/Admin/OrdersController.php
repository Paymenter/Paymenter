<?php

namespace App\Http\Controllers\Admin;

use App\Models\Orders;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Orders::all();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Orders $order)
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

    public function destroy(Orders $order)
    {
        if ($order->status == 'paid' || $order->status == 'suspended') {
            ExtensionHelper::terminateServer($order);
        }
        $order->delete();

        return redirect()->route('admin.orders')->with('success', 'Order deleted');
    }

    public function suspend(Orders $order)
    {
        $order->status = 'suspended';
        $order->save();
        ExtensionHelper::suspendServer($order);

        return redirect()->route('admin.orders.show', $order);
    }

    public function unsuspend(Orders $order)
    {
        $order->status = 'paid';
        $order->save();
        ExtensionHelper::unsuspendServer($order);

        return redirect()->route('admin.orders.show', $order);
    }

    public function create(Orders $order)
    {
        $order->status = 'paid';
        $order->save();
        ExtensionHelper::createServer($order);

        return redirect()->route('admin.orders.show', $order);
    }

    public function paid(Orders $order)
    {
        $order->status = 'paid';
        $order->save();
        ExtensionHelper::createServer($order);

        return redirect()->route('admin.orders.show', $order);
    }
}
