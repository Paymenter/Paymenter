<?php

namespace App\Http\Requests\Api\Admin\TicketMessages;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class GetTicketMessagesRequest extends AdminApiRequest
{
    protected $permission = 'ticket_messages.view';
}
