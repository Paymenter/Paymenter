<?php

namespace App\Http\Controllers;

use App\Models\{Announcement, Category, Product};
use Illuminate\Http\Request;

class BasisController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        $announcements = Announcement::where('published', 1)->get();

        return view('welcome', compact('categories', 'announcements'));
    }

    public function products(string $slug = null, Product $product = null)
    {
        if ($product) {
            return redirect()->route('checkout.add', $product->id);
        }

        $category = null;
        if ($slug != null) {
            $category = Category::where('slug', $slug)->first();
            if(!$category) {
                abort(404);
            }
        }

        $categories = Category::all();
        return view('product', compact('categories', 'category'));
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

    public function tos()
    {
        if(!config('settings::tos_text')) {
            abort(404);
        }
        return view('tos');
    }
}
