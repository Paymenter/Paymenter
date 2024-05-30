<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display the categories
     */
    public function index(): View
    {
        return view('admin.categories.index');
    }

    /**
     * Display the create form
     */
    public function create(): View
    {
        $categories = Category::all();

        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a new announcement
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5242',
            ]);
            // Public
            $request->image->store('categories', 'public');

        }

        Category::create([
            'name' => $request['name'],
            'description' => $request['description'],
            'slug' => $request['slug'],
            'category_id' => $request['parent_id'],
            'image' => $request->image ? $request->image->hashName() : null,
        ]);

        return redirect()->route('admin.categories');
    }

    /**
     * Display edit form
     */
    public function edit(Category $category): View
    {
        $categories = Category::where('id', '!=', $category->id)->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the category
     *
     * @throws ValidationException
     */
    public function update(Category $category, Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image',
            'remove_image' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5242',
            ]);
            // Public
            $request->image->store('categories', 'public');

        }

        if ($request->remove_image == 'on') {
            $category->image = null;
        } else {
            $category->image = $request->image ? $request->image->hashName() : $category->image;
        }

        $category->update([
            'name' => $request['name'],
            'description' => $request['description'],
            'slug' => $request['slug'],
            'category_id' => $request['parent_id'],
        ]);

        return redirect()->route('admin.categories.edit', $category);
    }

    /**
     * Delete the category
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories');
    }
}
