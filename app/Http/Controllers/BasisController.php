<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class BasisController extends Controller
{
    function index()
    {
        //$services = Services::where('client', auth()->user()->id)->get();
        $categories = Categories::all();
        return view('welcome', compact('categories'));
    }

    function products(Request $request)
    {   

        if($request->has('category')){
            $category = $request->input('category');
            $categories = Categories::where('id', $category)->get();
        }else{
            $categories = Categories::all();
        }
        return view('product', compact('categories'));
    }
}
