<?php

namespace App\Http\Requests\Api\Admin\Services;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class DeleteServiceRequest extends AdminApiRequest
{
    protected $permission = 'services.delete';
}
