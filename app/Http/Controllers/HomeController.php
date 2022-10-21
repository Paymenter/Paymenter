<?php
namespace App\Http\Controllers;
use App\Models\Orders;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    function index()
    {
        $services = Orders::where('client', auth()->user()->id)->get();
        return view('home', compact('services'));
    }

    function manifest(Request $request)
    {
        error_log($request->input('test'));
        return response()->json([
            'author_name' => $request->input('author_name'),
            'author_url' => $request->input('author_url'),
            'provider_name' => $request->input('provider_name'),
            'provider_url' => $request->input('provider_url'),
            'title' => $request->input('title'),
        ]);
        
    }
}