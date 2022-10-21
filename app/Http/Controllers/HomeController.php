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
        $json = json_encode($request->input(), JSON_UNESCAPED_SLASHES);
        echo $json;
        return;
    }
}