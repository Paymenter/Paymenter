<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store()
    {
        $data = request()->validate([
            'name' => 'required',
            'description' => 'required',
            'slug' => 'required',
        ]);

        Category::create($data);

        return redirect()->route('admin.categories');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Category $category)
    {
        $data = request()->validate([
            'name' => 'required',
            'description' => 'required',
            'slug' => 'required',
        ]);

        $category->update($data);

        return redirect()->route('admin.categories');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories');
    }
}
