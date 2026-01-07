<?php

namespace App\Http\Requests\Api\Admin\Products;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use Illuminate\Validation\Rule;

class CreateProductRequest extends AdminApiRequest
{
    protected $permission = 'products.create';

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug'),
            ],
            'stock' => 'nullable|integer|min:0',
            'per_user_limit' => 'nullable|integer|min:0',
            'allow_quantity' => 'required|in:disabled,separated,combined',
            'email_template' => 'nullable|string',
            'hidden' => 'nullable|boolean',
        ];
    }
}
