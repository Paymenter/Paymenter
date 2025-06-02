<?php

namespace App\Http\Requests\Api\Admin\Users;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends AdminApiRequest
{
    protected $permission = 'users.delete';
}
