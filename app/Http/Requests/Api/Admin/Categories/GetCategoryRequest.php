<?php

namespace App\Http\Requests\Api\Admin\Categories;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetCategoryRequest extends AdminApiRequest
{
    protected $permission = 'categories.view';
}
