<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class BasisController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return view('welcome', compact('categories'));
    }

    public function products(string $slug = null, Product $product = null)
    {
        if ($product) {
            return redirect()->route('checkout.add', $product->id);
        }
        if ($slug == null) {
            $categories = Category::all();
        } else {
            $categories = Category::where('slug', $slug)->get();
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
