<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Categories;
use Illuminate\Http\Request;

class BasisController extends Controller
{
    public function index()
    {
        $categories = Categories::all();

        return view('welcome', compact('categories'));
    }

    public function products(string $slug = null, Products $product = null)
    {
        if ($product) {
            return redirect()->route('checkout.add', $product->id);
        }
        if ($slug == null) {
            $categories = Categories::all();
        } else {
            $categories = Categories::where('slug', $slug)->get();
        }
        if ($categories->count() == 0) {
            return abort(404);
        }

        return view('product', compact('categories'));
    }

    public function manifest(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $value = htmlentities($value);
            $request->merge([$key => $value]);
        }
        $json = json_encode($request->all(), JSON_UNESCAPED_SLASHES);
        echo $json;
    }
}
