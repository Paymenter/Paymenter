<?php

namespace App\Http\Requests\Api\Admin\Credits;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetCreditRequest extends AdminApiRequest
{
    protected $permission = 'credits.view';
}
