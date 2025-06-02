<?php

namespace App\Http\Requests\Api\Admin\Users;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class GetUserRequest extends AdminApiRequest
{
    protected $permission = 'users.viewAny';
}
