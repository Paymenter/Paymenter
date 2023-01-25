<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Helpers\ExtensionHelper;
class OrdersController extends Controller
{
    public function index()
    {
        $orders = Orders::all();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Orders $id)
    {
        $order = $id;
        $products;
        // Loop through products
        foreach($order->products as $product){
            $link = ExtensionHelper::getLink($product);
            $product->link = $link;
            $products[] = $product;
        }
        return view('admin.orders.show', compact('order', 'products'));
    }

    public function destroy(Orders $id)
    {
        if($id->status == 'paid' || $id->status == 'suspended'){
            ExtensionHelper::terminateServer($id);
        }
        $order = $id;
        $order->delete();
        return redirect()->route('admin.orders')->with('success', 'Order deleted');
    }

    public function suspend(Orders $id)
    {
        $order = $id;
        $order->status = 'suspended';
        $order->save();
        ExtensionHelper::suspendServer($order);
        return redirect()->route('admin.orders.show', $order);
    }

    public function unsuspend(Orders $id)
    {
        $order = $id;
        $order->status = 'paid';
        $order->save();
        ExtensionHelper::unsuspendServer($order);
        return redirect()->route('admin.orders.show', $order);
    }

    public function create(Orders $id)
    {
        $order = $id;
        $order->status = 'paid';
        $order->save();
        ExtensionHelper::createServer($order);
        return redirect()->route('admin.orders.show', $order);
    }

    public function paid(Orders $id)
    {
        $order = $id;
        $order->status = 'paid';
        $order->save();
        ExtensionHelper::createServer($order);
        return redirect()->route('admin.orders.show', $order);
    }
}