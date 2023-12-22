<?php

namespace App\Http\Controllers\Clients;

use App\Models\OrderProduct;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Jobs\Servers\TerminateServer;
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
            'reason' => $request->reason,
        ]);

        return redirect()->back()->with('success', 'Product cancelled successfully.');
    }
}
