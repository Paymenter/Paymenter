<?php

namespace App\Http\Controllers\Admin;

use App\Models\Products;
use App\Models\Categories;
use App\Models\Extensions;
use Illuminate\Http\Request;
use App\Models\ProductSettings;
use App\Http\Controllers\Controller;

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
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5242',
        ]);
        if ($request->get('no_image')) {
            $data['image'] = 'null';
        } else {
            $imageName = time() . $request->get('category_id') . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = '/images/' . $imageName;
        }
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

        if ($request->hasFile('image') && !$request->get('no_image')) {
            $imageName = time() . '-' . $product->id . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = '/images/' . $imageName;
            if (file_exists(public_path() . $product->image)) {
                $image = unlink(public_path() . $product->image);
                if (!$image) {
                    error_log('Failed to delete image: ' . public_path() . $product->image);
                }
            }
        }

        if ($request->get('no_image')) {
            $data['image'] = 'null';
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
        $extensions = Extensions::where('type', 'server')->where('enabled', true)->get();
        if ($product->server_id != null) {
            $server = Extensions::findOrFail($product->server_id);
            if (!file_exists(base_path('app/Extensions/Servers/' . $server->name . '/index.php'))) {
                $server = null;
                $extension = null;

                return view('admin.products.extension', compact('product', 'extensions', 'server', 'extension'))->with('error', 'Extension not found');
            }
            include_once base_path('app/Extensions/Servers/' . $server->name . '/index.php');
            $extension = new \stdClass();
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
        // Check if only the server has been changed
        if ($product->server_id != $request->input('server_id')) {
            // Delete all product settings
            ProductSettings::where('product_id', $product->id)->delete();
            $product->update($data);
            return redirect()->route('admin.products.extension', $product->id)->with('success', 'Server changed successfully');
        }

        include_once base_path('app/Extensions/Servers/' . $product->server()->get()->first()->name . '/index.php');
        $extension = new \stdClass();
        $function = $product->server()->get()->first()->name . '_getProductConfig';
        $extension2 = json_decode(json_encode($function()));
        $extension->productConfig = $extension2;
        foreach ($extension->productConfig as $config) {
            $config->required = isset($config->required) ? $config->required : false;
            if($config->required && !$request->input($config->name)) {
                return redirect()->route('admin.products.extension', $product->id)->with('error', 'Please fill in all required fields');
            }
            ProductSettings::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'name' => $config->name,
                    'extension' => $product->server()->get()->first()->id,
                ],
                [
                    'product_id' => $product->id,
                    'name' => $config->name,
                    'value' => $request->input($config->name),
                    'extension' => $product->server()->get()->first()->id,
                ]
            );
        }

        return redirect()->route('admin.products.extension', $product->id)->with('success', 'Product updated successfully');
    }
}
