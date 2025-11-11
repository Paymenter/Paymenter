<?php

namespace App\Http\Requests\Api\Admin\Credits;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class DeleteCreditRequest extends AdminApiRequest
{
    protected $permission = 'credits.delete';
}
