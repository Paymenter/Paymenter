<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{

    /**
     * Display the categories
     *
     * @return View
     */
    public function index(): View
    {
        return view('admin.categories.index');
    }

    /**
     * Display the create form
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a new announcement
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedRequest = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:categories,slug',
        ]);

        Category::create($validatedRequest->validated());

        return redirect()->route('admin.categories');
    }

    /**
     * Display edit form
     *
     * @param Category $category
     * @return View
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the category
     *
     * @param Category $category
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function update(Category $category, Request $request): RedirectResponse
    {
        $validatedRequest = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id,
        ]);

        $category->update($validatedRequest->validated());

        return redirect()->route('admin.categories');
    }

    /**
     * Delete the category
     *
     * @param Category $category
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories');
    }
}
