<?php

namespace App\Http\Requests\Api\Admin\Services;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class GetServiceRequest extends AdminApiRequest
{
    protected $permission = 'services.view';
}
