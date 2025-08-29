<?php

namespace App\Http\Requests\Api\Admin\TicketMessages;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class DeleteTicketMessageRequest extends AdminApiRequest
{
    protected $permission = 'ticket_messages.delete';
}
