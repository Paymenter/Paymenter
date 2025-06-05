<?php

namespace App\Http\Requests\Api\Admin\Services;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class DeleteServiceRequest extends AdminApiRequest
{
    protected $permission = 'services.delete';
}
