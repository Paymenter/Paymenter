<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Tickets;
use App\Models\Statistics;

class MainController extends Controller
{
    function __construct()
    {   
        $this->middleware('auth.admin');
    }

    function index()
    {
        $tickets = Statistics::where('name', 'tickets')->where('created_at', '<', now())->orderBy('date', 'desc')->limit(7)->count();
        $ticketsClosed = Statistics::where('name', 'ticketsClosed')->where('created_at', '<', now())->orderBy('date', 'desc')->count();
        $orders = Statistics::where('name', 'orders')->where('created_at', '<', now())->orderBy('date', 'desc')->count();
        error_log($tickets);
        error_log(date('Y-m-d'));
        return view('admin.index', compact('orders', 'tickets', 'ticketsClosed'));
    }
}
