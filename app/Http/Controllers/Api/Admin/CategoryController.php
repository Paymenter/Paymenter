<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Admin\Categories\GetCategoriesRequest;
use App\Http\Requests\Api\Admin\Categories\GetCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group(name: 'Categories', weight: 1)]
class CategoryController extends ApiController
{
    protected const INCLUDES = [
        'products',
        'parent',
        'children',
    ];

    /**
     * List Categories
     */
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(GetCategoriesRequest $request)
    {
        // Fetch categories with pagination
        $categories = QueryBuilder::for(Category::class)
            ->allowedFilters(['name', 'parent_id'])
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->allowedSorts(['id', 'created_at', 'updated_at', 'name'])
            ->simplePaginate(request('per_page', 15));

        // Return the categories as a JSON response
        return CategoryResource::collection($categories);
    }

    /**
     * Show a specific category
     */
    public function show(GetCategoryRequest $request, Category $category)
    {
        $category = QueryBuilder::for(Category::class)
            ->allowedIncludes($this->allowedIncludes(self::INCLUDES))
            ->findOrFail($category->id);

        // Return the category as a JSON response
        return new CategoryResource($category);
    }
}
