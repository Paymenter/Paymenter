<?php

namespace App\Http\Requests\Api\Admin\Tickets;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetTicketsRequest extends AdminApiRequest
{
    protected $permission = 'tickets.view';
}
