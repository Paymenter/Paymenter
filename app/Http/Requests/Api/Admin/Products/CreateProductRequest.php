<?php

namespace App\Http\Requests\Api\Admin\Products;

use App\Http\Requests\Api\Admin\AdminApiRequest;

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
                function ($attribute, $value, $fail) {
                    if (!empty($value) && \App\Models\Product::where('slug', $value)->exists()) {
                        $fail('The slug already exists.');
                    }
                },
            ],

        ];
    }
}
