<?php
namespace App\Http\Controllers;
use App\Models\Orders;

class HomeController extends Controller
{
    function index()
    {
        $services = Orders::where('client', auth()->user()->id)->get();
        return view('home', compact('services'));
    }
}