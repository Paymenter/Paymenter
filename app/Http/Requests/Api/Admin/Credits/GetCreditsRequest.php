<?php

namespace App\Http\Requests\Api\Admin\Credits;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetCreditsRequest extends AdminApiRequest
{
    protected $permission = 'credits.view';
}
