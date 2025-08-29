<?php

namespace App\Http\Requests\Api\Admin\Invoices;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class DeleteInvoiceRequest extends AdminApiRequest
{
    protected $permission = 'invoices.delete';
}
