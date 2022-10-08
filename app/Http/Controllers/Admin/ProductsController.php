<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;
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
