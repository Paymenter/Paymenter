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

    public function show($id)
    {
        return view('admin.products.show');
    }

    public function edit($id)
    {
        return view('admin.products.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.products');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.products');
    }
}
