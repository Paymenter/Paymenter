<?php

namespace App\Http\Controllers;

use App\Models\{Announcement, Category, FileUpload, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BasisController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->orderBy('order')->get();
        $announcements = Announcement::where('published', 1)->get();

        return view('welcome', compact('categories', 'announcements'));
    }

    public function products(string $slug = null, Product $product = null)
    {
        if ($product) {
            return redirect()->route('checkout.config', $product);
        }

        $category = null;
        if ($slug != null) {
            $category = Category::where('slug', $slug)->first();
            if (!$category) {
                abort(404);
            }
            if ($category->products()->where('hidden', 0)->count() == 0 && $category->children()->count() == 0) {
                abort(404);
            }
        }

        $categories = Category::whereNull('category_id')->with('products')->orderBy('order')->get();
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
        if (!config('settings::tos_text')) {
            abort(404);
        }
        return view('tos');
    }

    public function downloadFile(FileUpload $fileUpload)
    {
        if (!Storage::exists('uploads/' . $fileUpload->uuid . '.' . $fileUpload->extension)) {
            abort(404);
        }
        return response()->download(storage_path('app/uploads/' . $fileUpload->uuid . '.' . $fileUpload->extension), $fileUpload->filename);
    }
}
