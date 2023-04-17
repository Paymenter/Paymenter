<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Extension;
use Illuminate\Http\Request;
use App\Models\ProductSetting;
use App\Http\Controllers\Controller;
use App\Models\ProductPrice;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return view('admin.products.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::all();

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
        $product = Product::create($data);
        ProductPrice::create([
            'product_id' => $product->id,
            'monthly' => $data['price'],
            'type' => $data['price'] > 0 ? 'monthly' : 'free',
        ]);

        return redirect()->route('admin.products.edit', $product->id)->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = request()->validate([
            'name' => 'required',
            'description' => 'required|string|min:10',
            'category_id' => 'required|integer',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5242',
            'stock' => 'integer|required_if:stock_enabled,true',
            'stock_enabled' => 'boolean',
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
        $product->stock_enabled = $request->get('stock_enabled') ?? false;

        if ($request->get('no_image')) {
            $data['image'] = 'null';
        }
        $product->update($data);

        return redirect()->route('admin.products.edit', $product->id)->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->prices()->delete();
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function pricing(Product $product)
    {
        $pricing = ProductPrice::where('product_id', $product->id)->first();
        return view('admin.products.pricing', compact('product', 'pricing'));
    }

    public function pricingUpdate(Request $request, Product $product)
    {
        $request->validate([
            'pricing' => 'required|in:recurring,free,one-time',
            'allow_quantity' => 'in:0,1,2'
        ]);
        if($request->get('pricing') !== $product->prices()->get()->first()->type){
            $request->validate([
                'pricing' => 'required|in:recurring,free,one-time'
            ]);
            // Update it
            $product->prices()->update([
                'type' => $request->get('pricing')
            ]);

            return redirect()->route('admin.products.pricing', $product->id)->with('success', 'Product pricing updated successfully');
        }
        $product->prices()->update(
            [
                'monthly' => $request->get('monthly'),
                'quarterly' => $request->get('quarterly'),
                'semi_annually' => $request->get('semi_annually'),
                'annually' => $request->get('annually'),
                'biennially' => $request->get('biennially'),
                'triennially' => $request->get('triennially'),
                'monthly_setup' => $request->get('monthly_setup'),
                'quarterly_setup' => $request->get('quarterly_setup'),
                'semi_annually_setup' => $request->get('semi_annually_setup'),
                'annually_setup' => $request->get('annually_setup'),
                'biennially_setup' => $request->get('biennially_setup'),
                'triennially_setup' => $request->get('triennially_setup'),
            ]
        );
        $product->update([
            'allow_quantity' => $request->get('allow_quantity'),
        ]);

        return redirect()->route('admin.products.pricing', $product->id)->with('success', 'Product pricing updated successfully');
    }

    public function extension(Product $product)
    {
        $extensions = Extension::where('type', 'server')->where('enabled', true)->get();
        if ($product->server_id != null) {
            $server = Extension::findOrFail($product->server_id);
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

    public function extensionUpdate(Request $request, Product $product)
    {
        $data = request()->validate([
            'server_id' => 'required|integer',
        ]);
        // Check if only the server has been changed
        if ($product->server_id != $request->input('server_id')) {
            // Delete all product settings
            ProductSetting::where('product_id', $product->id)->delete();
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
            if ($config->required && $request->input($config->name) == null) {
                return redirect()->route('admin.products.extension', $product->id)->with('error', 'Please fill in all required fields');
            }
            ProductSetting::updateOrCreate(
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

    public function extensionExport(Product $product)
    {
        $server = Extension::findOrFail($product->server_id);
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

        $productSettings = ProductSetting::where('product_id', $product->id)->get();
        $settings = [];
        $settings['!NOTICE!'] = 'This file was generated by Paymenter. Do not edit this file manually.';
        $settings['server'] = $server->name;

        foreach ($extension->productConfig as $config) {
            $productSettings2 = $productSettings->where('name', $config->name)->first();
            if ($productSettings2) {
                if (empty($productSettings2->value)) {
                    if ($config->type == 'text')
                        $settings['config'][$config->name] = '';
                    else if ($config->type == 'number')
                        $settings['config'][$config->name] = 0;
                    else if ($config->type == 'boolean')
                        $settings['config'][$config->name] = false;
                    else if ($config->type == 'select')
                        $settings['config'][$config->name] = $config->options[0];
                } else {
                    $settings['config'][$config->name] = $productSettings2->value;
                }
            } else {
                if ($config->type == 'text')
                    $settings['config'][$config->name] = '';
                else if ($config->type == 'number')
                    $settings['config'][$config->name] = 0;
                else if ($config->type == 'boolean')
                    $settings['config'][$config->name] = false;
                else if ($config->type == 'select')
                    $settings['config'][$config->name] = $config->options[0];
            }
        }

        // Export it as JSON
        $json = json_encode($settings, JSON_PRETTY_PRINT);
        $filename = $product->name . '.json';
        // Save the file
        return response(
            $json,
            200,
            [
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        );
    }

    public function extensionImport(Request $request, Product $product)
    {
        $request->validate([
            'json' => 'required|file|mimes:json',
        ]);
        // Move the file to the temp directory
        $file = $request->file('json');
        $file->move(storage_path('app/temp'), $file->getClientOriginalName());

        // Read the file
        $json = json_decode(file_get_contents(storage_path('app/temp/' . $file->getClientOriginalName())));
        // Delete the file
        unlink(storage_path('app/temp/' . $file->getClientOriginalName()));
        $server = Extension::where('name', $json->server)->first();
        if(!$server)
            return redirect()->route('admin.products.extension', $product->id)->with('error', 'Invalid server');
        if (!file_exists(base_path('app/Extensions/Servers/' . $server->name . '/index.php'))) {
            $server = null;
            $extension = null;

            return redirect()->route('admin.products.extension', $product->id)->with('error', 'Extension not found');
        }
        if ($product->server_id != $server->id)
            $product->update(['server_id' => $server->id]);
            
        include_once base_path('app/Extensions/Servers/' . $server->name . '/index.php');
        $extension = new \stdClass();
        $function = $server->name . '_getProductConfig';
        $extension2 = json_decode(json_encode($function()));
        $extension->productConfig = $extension2;
        $extension->name = $server->name;
        if (!$json) {
            return redirect()->route('admin.products.extension', $product->id)->with('error', 'Invalid JSON');
        }
        if (!isset($json->server) || $json->server != $server->name) {
            return redirect()->route('admin.products.extension', $product->id)->with('error', 'Invalid server');
        }
        if (!isset($json->config)) {
            return redirect()->route('admin.products.extension', $product->id)->with('error', 'Invalid config');
        }

        // Delete all product settings
        ProductSetting::where('product_id', $product->id)->delete();

        foreach ($extension->productConfig as $config) {
            if (isset($json->config->{$config->name})) {
                ProductSetting::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'name' => $config->name,
                        'extension' => $product->server()->get()->first()->id,
                    ],
                    [
                        'product_id' => $product->id,
                        'name' => $config->name,
                        'value' => $json->config->{$config->name},
                        'extension' => $product->server()->get()->first()->id,
                    ]
                );
            }
        }

        return redirect()->route('admin.products.extension', $product->id)->with('success', 'Product updated successfully');
    }

    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->name = $newProduct->name . ' (copy)';
        $newProduct->save();

        $productSettings = ProductSetting::where('product_id', $product->id)->get();
        foreach ($productSettings as $productSetting) {
            $newProductSetting = $productSetting->replicate();
            $newProductSetting->product_id = $newProduct->id;
            $newProductSetting->save();
        }

        $productPrice = ProductPrice::where('product_id', $product->id)->get()->first();
        if ($productPrice) {
            $newProductPrice = $productPrice->replicate();
            $newProductPrice->product_id = $newProduct->id;
            $newProductPrice->save();
        }

        return redirect()->route('admin.products.edit', $newProduct->id)->with('success', 'Product duplicated successfully');
    }
}
