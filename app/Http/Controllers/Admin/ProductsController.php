<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Products;
use App\Models\Extensions;
use App\Models\ProductSettings;

class ProductsController extends Controller
{
    public function index()
    {
        $categories = Categories::all();
        return view('admin.products.index', compact('categories'));
    }

    public function create()
    {
        $categories = Categories::all();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'name' => 'required',
            'description' => 'required|string|min:10',
            'price' => 'required',
            'category_id' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5242',
        ]);
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);
        $data['image'] = '/images/' . $imageName;
        Products::create($data);
        return redirect()->route('admin.products');
    }

    public function edit(Products $product)
    {
        $categories = Categories::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Products $product)
    {
        $data = request()->validate([
            'name' => 'required',
            'description' => 'required|string|min:10',
            'price' => 'required',
            'category_id' => 'required|integer',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5242',
        ]);
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = '/images/' . $imageName;
        }
        $product->update($data);
        return redirect()->route('admin.products.edit', $product->id)->with('success', 'Product updated successfully');
    }

    public function destroy(Products $product)
    {
        $product = Products::find($id);
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function extension(Products $product)
    {
        $extensions = Extensions::where("type", "server")->where('enabled', true)->get();
        if($product->server_id != null) {
            $server = Extensions::find($product->server_id);
            $extension = json_decode(file_get_contents(base_path('app/Extensions/Servers/' . $server->name . '/extension.json')));
        } else {
            $server = null;
            $extension = null;
        }

        return view('admin.products.extension', compact('product', 'extensions', 'server', 'extension'));
    }

    public function extensionUpdate(Request $request, Products $product)
    {
        $data = request()->validate([
            'server_id' => 'required|integer',
        ]);
        $product->update($data);

        $extension = json_decode(file_get_contents(base_path('app/Extensions/Servers/' . $product->server()->get()->first()->name . '/extension.json')));
        foreach ($extension->productConfig as $config) {
            error_log($request->input($config->name));
            ProductSettings::updateOrCreate([
                'product_id' => $product->id,
                'name' => $config->name,
                'extension' => $product->server()->get()->first()->name,
            ],
            [
                'product_id' => $product->id,
                'name' => $config->name,
                'value' => $request->input($config->name),
                'extension' => $product->server()->get()->first()->id,
            ]);
        }

        return redirect()->route('admin.products.extension', $product->id)->with('success', 'Product updated successfully');
    }
}
