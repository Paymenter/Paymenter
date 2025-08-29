<?php

namespace App\Http\Requests\Api\Admin\Services;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetServicesRequest extends AdminApiRequest
{
    protected $permission = 'services.view';
}
