<?php

namespace App\Http\Requests\Api\Admin\Tickets;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class DeleteTicketRequest extends AdminApiRequest
{
    protected $permission = 'tickets.delete';
}
