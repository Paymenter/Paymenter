<?php

namespace App\Http\Controllers\API\Website;

use App\Classes\API;
use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Get all Categories.
     */
    public function getCategories()
    {
        $category = Category::paginate(25);

        return response()->json([
            'categories' => API::repaginate($category),
        ], 200);
    }
}
