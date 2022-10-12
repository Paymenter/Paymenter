<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Products;

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

    public function edit($id)
    {
        $product = Products::find($id);
        $categories = Categories::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $data = request()->validate([
            'name' => 'required',
            'description' => 'required|string|min:10',
            'price' => 'required',
            'category_id' => 'required|integer',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5242',
        ]);
        $product = Products::find($id);
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = '/images/' . $imageName;
        }
        $product->update($data);
        return redirect()->route('admin.products.edit', $id)->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $product = Products::find($id);
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }
}
