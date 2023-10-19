<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;

class MainController extends Controller
{
    public function index()
    {
        $revenueTotal = 0;
        $invoices = Invoice::where('status', 'paid')->with(['items.product.order.coupon', 'items.product.product'])->get();
        foreach ($invoices as $invoice) {
            $revenueTotal += $invoice->total();
        }

        $orders = Order::selectRaw('DATEDIFF(CURDATE(), DATE(created_at)) as day, COUNT(*) as count')
            ->whereRaw('created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)')
            ->groupBy('day')
            ->get();

        $orderCounts = $orders->pluck('count', 'day')->toArray();

        $users = User::selectRaw('DATEDIFF(CURDATE(), DATE(created_at)) as day, COUNT(*) as count')
            ->whereRaw('created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)')
            ->groupBy('day')
            ->get();

        $userCounts = $users->pluck('count', 'day')->toArray();

        $invoices = Invoice::selectRaw('DATEDIFF(CURDATE(), DATE(invoices.created_at)) as day, SUM(invoice_items.total) as count')
            ->whereRaw('invoices.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->groupBy('day')
            ->get();
                
        $invoiceCounts = $invoices->pluck('count', 'day')->toArray();


        return view('admin.index', compact('revenueTotal', 'orderCounts', 'userCounts', 'invoiceCounts'));
    }
}
