<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;

class BasisController extends Controller
{
    function index()
    {
        //$services = Services::where('client', auth()->user()->id)->get();
        $categories = Categories::all();
        return view('welcome', compact('categories'));
    }

    function products(String $slug = null, Products $product = null)
    {
        if ($product) {
            return view('checkout', compact('product'));
        } else {
            if($slug == null)
                $categories = Categories::all();
            else if($slug == 'products')
                $categories = Categories::all();
            else
                $categories = Categories::where('slug', $slug)->get();
            if($categories->count() == 0)
                return abort(404);
            return view('product', compact('categories'));
        }
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
