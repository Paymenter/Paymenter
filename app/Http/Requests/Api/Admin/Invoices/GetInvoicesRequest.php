<?php

namespace App\Http\Requests\Api\Admin\Invoices;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetInvoicesRequest extends AdminApiRequest
{
    protected $permission = 'invoices.view';
}
