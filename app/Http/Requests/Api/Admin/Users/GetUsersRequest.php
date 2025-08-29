<?php

namespace App\Http\Requests\Api\Admin\Users;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetUsersRequest extends AdminApiRequest
{
    protected $permission = 'users.view';
}
