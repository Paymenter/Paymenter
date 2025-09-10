<?php

namespace App\Http\Requests\Api\Admin\InvoiceItems;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetInvoiceItemsRequest extends AdminApiRequest
{
    protected $permission = 'invoice_items.view';
}
