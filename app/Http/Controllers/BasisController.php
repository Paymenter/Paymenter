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

    function manifest(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $value = htmlentities($value);
            $request->merge([$key => $value]);
        }
        $json = json_encode($request->all(), JSON_UNESCAPED_SLASHES);
        echo $json;
    }
}
