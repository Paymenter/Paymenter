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
        $categories = Category::all()->sortBy('order');

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Reorder the categories
     * 
     * @param Request $request
     * @return void
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:categories,id',
            'newIndex' => 'required|integer|min:0',
            'oldIndex' => 'required|integer|min:0',
        ]);
        $newIndex = $request->input('newIndex');
        $oldIndex = $request->input('oldIndex');
        if ($newIndex == $oldIndex) {
            return response()->json(['success' => true]);
        }
        $categories = Category::all()->sortBy('order');
        $category = $categories->where('id', $request->input('id'))->first();
        $category->order = $newIndex;
        $category->save();

        // If the new index is greater than the old index, we need to shift all the categories between the old and new index up one
        if ($newIndex > $oldIndex) {
            foreach ($categories as $c) {
                if ($c->order > $oldIndex && $c->order <= $newIndex && $c->id != $category->id) {
                    $c->order--;
                    $c->save();
                }
            }
            // If the new index is less than the old index, we need to shift all the categories between the old and new index down one
        } else {
            foreach ($categories as $c) {
                if ($c->order < $oldIndex && $c->order >= $newIndex && $c->id != $category->id) {
                    $c->order++;
                    $c->save();
                }
            }
        }

        return response()->json(['success' => true]);
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
