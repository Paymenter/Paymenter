<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class MainController extends Controller
{
    public function index()
    {
        $revenueTotal = 0;
        $invoices = Invoice::where('status', 'paid')->get();
        foreach ($invoices as $invoice) {
            $revenueTotal += $invoice->order()->get()->first()->total();
        }

        return view('admin.index', compact('revenueTotal'));
    }
}
