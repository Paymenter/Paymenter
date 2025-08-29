<?php

namespace App\Http\Requests\Api\Admin\Services;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetServiceRequest extends AdminApiRequest
{
    protected $permission = 'services.view';
}
