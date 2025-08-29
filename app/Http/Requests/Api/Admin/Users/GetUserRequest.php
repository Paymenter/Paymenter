<?php

namespace App\Http\Requests\Api\Admin\Users;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetUserRequest extends AdminApiRequest
{
    protected $permission = 'users.view';
}
