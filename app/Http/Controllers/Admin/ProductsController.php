<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Products;
use App\Models\Extensions;
use App\Models\ProductSettings;
use stdClass;
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
        if($request->get('no_image')){
            $request->merge(['image' => null]);
        }
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);
        $data['image'] = '/images/' . $imageName;
        $product = Products::create($data);
        return redirect()->route('admin.products.edit', $product->id)->with('success', 'Product created successfully');
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
        if($request->get('no_image')){
            $request->merge(['image' => null]);
        }
        
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
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function extension(Products $product)
    {
        $extensions = Extensions::where("type", "server")->where('enabled', true)->get();
        if ($product->server_id != null) {
            $server = Extensions::findOrFail($product->server_id);
            include_once base_path('app/Extensions/Servers/' . $server->name . '/index.php');
            $extension = new stdClass;
            $function = $server->name . '_getProductConfig';
            $extension2 = json_decode(json_encode($function()));
            $extension->productConfig = $extension2;
            $extension->name = $server->name;
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

        include_once base_path('app/Extensions/Servers/' . $product->server()->get()->first()->name . '/index.php');
        $extension = new stdClass;
        $function = $product->server()->get()->first()->name . '_getProductConfig';
        $extension2 = json_decode(json_encode($function()));
        $extension->productConfig = $extension2;
        foreach ($extension->productConfig as $config) {
            ProductSettings::updateOrCreate([
                'product_id' => $product->id,
                'name' => $config->name,
                'extension' => $product->server()->get()->first()->id,
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
