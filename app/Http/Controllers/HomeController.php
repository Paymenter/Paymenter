<?php
namespace App\Http\Controllers;
use App\Models\Services;

class HomeController extends Controller
{
    function index()
    {
        $services = Services::where('client', auth()->user()->id)->get();
        return view('home', compact('services'));
    }
}