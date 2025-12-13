<?php

namespace App\Http\Requests\Api\Admin\Products;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class CreateProductRequest extends AdminApiRequest
{
    protected $permission = 'products.create';

    public function rules(): array
    {
        return [
            'name' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:255',
            'enabled' => 'sometimes|boolean',
            'category_id' => 'sometimes|nullable|exists:categories,id',
        ];
    }
}
