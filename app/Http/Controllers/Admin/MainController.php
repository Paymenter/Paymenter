<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Tickets;

class MainController extends Controller
{
    function __construct()
    {   
        $this->middleware('auth.admin');
    }

    function index()
    {
        return view('admin.index');
    }
}
