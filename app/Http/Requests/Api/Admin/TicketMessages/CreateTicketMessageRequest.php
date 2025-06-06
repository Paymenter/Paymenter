<?php

namespace App\Http\Requests\Api\Admin\TicketMessages;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class CreateTicketMessageRequest extends AdminApiRequest
{
    protected $permission = 'ticket_messages.create';

    public function rules(): array
    {
        return [
            'message' => 'required|string|max:5000',
            'user_id' => 'required|exists:users,id',
            'ticket_id' => 'required|exists:tickets,id',
        ];
    }
}
