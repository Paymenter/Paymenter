<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Services;
use App\Models\Tickets;

class MainController extends Controller
{
    function __construct()
    {   
        $this->middleware('auth.admin');
    }

    function index()
    {
        $services = Services::all();
        $ticketCount = Tickets::all()->count();
        return view('admin.index', compact('services', 'ticketCount'));
    }
}
