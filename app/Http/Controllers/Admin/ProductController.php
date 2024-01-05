<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ExtensionHelper;
use App\Models\Product;
use App\Models\Category;
use App\Models\Extension;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\ProductSetting;
use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\ProductPrice;
use Illuminate\View\View;

class ProductController extends Controller
{

    /**
     *  Display a listing of the view
     *
     * @return View
     */
    public function index(): View
    {
        $categories = Category::orderBy('order', 'asc')->get();

        return view('admin.products.index', compact('categories'));
    }

    /**
     * Display the creating form
     *
     * @return View
     */
    public function create(): View
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a new product
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
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
            'type' => $data['price'] > 0 ? 'recurring' : 'free',
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
            'name' => 'required|string',
            'description' => 'required|string',
            'category_id' => 'required|integer',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5242',
            'stock' => 'integer|required_if:stock_enabled,true',
            'stock_enabled' => 'boolean',
            'hidden' => 'boolean',
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
        $product->hidden = $request->get('hidden') ?? false;

        if ($request->get('no_image')) {
            $data['image'] = 'null';
        }
        $product->update($data);

        return redirect()->route('admin.products.edit', $product->id)->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product): \Illuminate\Http\RedirectResponse
    {
        OrderProduct::where('product_id', $product->id)->delete();
        ProductPrice::where('product_id', $product->id)->delete();
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }

    public function pricing(Product $product)
    {
        $pricing = $product->prices;
        return view('admin.products.pricing', compact('product', 'pricing'));
    }

    public function pricingUpdate(Request $request, Product $product)
    {
        $request->validate([
            'pricing' => 'required|in:recurring,free,one-time',
            'allow_quantity' => 'in:0,1,2',
            'limit' => 'nullable|integer',
        ]);
        if ($request->get('pricing') !== $product->prices->type) {
            $request->validate([
                'pricing' => 'required|in:recurring,free,one-time'
            ]);
            // Update it
            $product->prices->update([
                'type' => $request->get('pricing')
            ]);

            return redirect()->route('admin.products.pricing', $product->id)->with('success', 'Product pricing updated successfully');
        }
        $product->prices->update(
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
            'limit' => $request->get('limit'),
        ]);

        return redirect()->route('admin.products.pricing', $product->id)->with('success', 'Product pricing updated successfully');
    }

    public function extension(Product $product)
    {
        $extensions = Extension::where('type', 'server')->where('enabled', true)->get();
        if ($product->extension_id != null) {
            $extension = Extension::findOrFail($product->extension_id);
            $config = [];
            try {
                $config = ExtensionHelper::getProductConfiguration($product);
            } catch (\Exception $error) {
                $extension->productConfig = [];
                session()->flash('error', $extension->name . ' threw an error: ' . $error->getMessage() . ' (are your extension settings correct?)');
                return view('admin.products.extension', compact('product', 'extensions', 'extension'));
            }
            $extension->productConfig = $config;
        } else {
            $server = null;
            $extension = null;
        }
        return view('admin.products.extension', compact('product', 'extensions', 'extension'));
    }

    public function extensionUpdate(Request $request, Product $product)
    {
        $data = request()->validate([
            'extension_id' => 'required|integer',
        ]);
        // Check if only the server has been changed
        if ($product->extension_id != $request->input('extension_id')) {
            // Delete all product settings
            ProductSetting::where('product_id', $product->id)->delete();
            $product->update($data);
            return redirect()->route('admin.products.extension', $product->id)->with('success', 'Server changed successfully');
        }
        $extension = Extension::findOrFail($product->extension_id);

        $config = ExtensionHelper::getProductConfiguration($product);
        $extension->productConfig = $config;

        foreach ($extension->productConfig as $config) {
            if ($config->type == 'title') continue;
            $config->required = isset($config->required) ? $config->required : false;
            if ($config->required && $request->input($config->name) == null) {
                return redirect()->route('admin.products.extension', $product->id)->with('error', 'Please fill in all required fields');
            }
            ProductSetting::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'name' => $config->name,
                    'extension' => $product->extension->id,
                ],
                [
                    'product_id' => $product->id,
                    'name' => $config->name,
                    'value' => $request->input($config->name),
                    'extension' => $product->extension->id,
                ]
            );
        }

        return redirect()->route('admin.products.extension', $product->id)->with('success', 'Product updated successfully');
    }

    public function extensionExport(Product $product)
    {
        $extension = Extension::findOrFail($product->extension_id);
        if (!$extension) {
            return view('admin.products.extension', compact('product', 'extensions', 'server', 'extension'))->with('error', 'Extension not found');
        }

        $config = ExtensionHelper::getProductConfiguration($product);
        $extension->productConfig = $config;


        $productSettings = ProductSetting::where('product_id', $product->id)->get();
        $settings = [];
        $settings['!NOTICE!'] = 'This file was generated by Paymenter. Do not edit this file manually.';
        $settings['server'] = $extension->name;

        foreach ($extension->productConfig as $config) {
            if ($config->type == 'title') continue;
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
        if (!$server)
            return redirect()->route('admin.products.extension', $product->id)->with('error', 'Invalid server');
        if ($product->extension_id != $server->id)
            $product->update(['extension_id' => $server->id]);
        
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
        $econfig = ExtensionHelper::getProductConfiguration($product);

        foreach ($econfig as $config) {
            if (isset($json->config->{$config->name})) {
                ProductSetting::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'name' => $config->name,
                        'extension' => $product->extension->id,
                    ],
                    [
                        'product_id' => $product->id,
                        'name' => $config->name,
                        'value' => $json->config->{$config->name},
                        'extension' => $product->extension->id,
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

    public function upgrade(Product $product)
    {
        $products = Product::where('id', '!=', $product->id)->get();

        return view('admin.products.upgrade', compact('product', 'products'));
    }

    public function upgradeUpdate(Request $request, Product $product)
    {
        $request->validate([
            'upgrades' => 'array',
            'upgrade_configurable_options' => 'boolean'
        ]);

        $product->update([
            'upgrade_configurable_options' => $request->get('upgrade_configurable_options', 0),
        ]);

        foreach ($product->upgrades as $upgrade) {
            if (!in_array($upgrade->upgrade_product_id, $request->get('upgrades', []))) {
                $upgrade->delete();
            }
        }

        foreach ($request->get('upgrades', []) as $upgrade) {
            $product->upgrades()->updateOrCreate(
                [
                    'product_id' => $product->id,
                    'upgrade_product_id' => $upgrade,
                ],
                [
                    'product_id' => $product->id,
                    'upgrade_product_id' => $upgrade,
                ]
            );
        }

        return redirect()->route('admin.products.upgrade', $product->id)->with('success', 'Product upgrades updated successfully');
    }
}
