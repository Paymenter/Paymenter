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
        return view('admin.orders.show', compact('order'));
    }

    public function destroy(Orders $id)
    {
        if($id->status == 'paid' || $id->status == 'suspended'){
            ExtensionHelper::terminateServer($id);
        }
        $order = $id;
        $order->delete();
        return redirect()->route('admin.orders');
    }
}