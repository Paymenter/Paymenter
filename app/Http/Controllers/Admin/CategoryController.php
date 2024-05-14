<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

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
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a new category
     *
     * @return RedirectResponse
     */
     public function store(Request $request): RedirectResponse
     {
         $request->validate([
             'name' => 'required',
             'description' => 'required',
             'slug' => 'required|unique:categories,slug',
             'parent_id' => 'nullable|exists:categories,id',
             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5242',
         ]);
 
         $imageUrl = null;
         if ($request->hasFile('image')) {
             $disk = env('FILESYSTEM_DISK', 'local');
 
             if ($disk === 's3') {
                 // Загрузка изображения в S3
                 $imagePath = $request->file('image')->store('categories', 's3');
                 // Установление публичных прав
                 Storage::disk('s3')->setVisibility($imagePath, 'public');
                 // Получение полного URL загруженного изображения
                 $imageUrl = Storage::disk('s3')->url($imagePath);
             } else {
                 // Загрузка изображения в локальное хранилище
                 $imagePath = $request->file('image')->store('categories', 'public');
                 // Получение полного URL загруженного изображения
                 $imageUrl = Storage::disk('public')->url($imagePath);
             }
         }
 
         Category::create([
             'name' => $request->input('name'),
             'description' => $request->input('description'),
             'slug' => $request->input('slug'),
             'category_id' => $request->input('parent_id'),
             'image' => $imageUrl,
         ]);
 
         return redirect()->route('admin.categories')->with('success', 'Category created successfully');
     }

    /**
     * Display edit form
     *
     * @param Category $category
     * @return View
     */
    public function edit(Category $category): View
    {
        $categories = Category::where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'categories'));
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
         $request->validate([
             'name' => 'required',
             'description' => 'required',
             'slug' => 'required|unique:categories,slug,' . $category->id,
             'parent_id' => 'nullable|exists:categories,id',
             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5242',
             'remove_image' => 'nullable',
         ]);
 
         // Обновление полей категории
         $category->name = $request->input('name');
         $category->description = $request->input('description');
         $category->slug = $request->input('slug');
         $category->category_id = $request->input('parent_id');
 
         $disk = env('FILESYSTEM_DISK', 'local');
 
         if ($request->hasFile('image')) {
             // Валидация загружаемого изображения
             $request->validate([
                 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5242',
             ]);
 
             // Загрузка нового изображения в выбранное хранилище
             if ($disk === 's3') {
                 $imagePath = $request->file('image')->store('categories', 's3');
                 // Установление публичных прав
                 Storage::disk('s3')->setVisibility($imagePath, 'public');
                 $imageUrl = Storage::disk('s3')->url($imagePath);
             } else {
                 $imagePath = $request->file('image')->store('categories', 'public');
                 $imageUrl = Storage::disk('public')->url($imagePath);
             }
             // Обновление поля изображения с полным URL
             $category->image = $imageUrl;
 
         } elseif ($request->has('remove_image')) {
             // Обработка удаления изображения
             $category->image = null;
         }
 
         // Сохранение обновленной категории
         $category->save();
 
         return redirect()->route('admin.categories.edit', $category)->with('success', 'Category updated successfully');
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

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully');
    }
}
