<?php

namespace App\Http\Requests\Api\Admin\TicketMessages;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use App\Models\Ticket;
use Illuminate\Validation\Validator;

class CreateTicketMessageRequest extends AdminApiRequest
{
    protected $permission = 'ticket_messages.create';

    public function rules(): array
    {
        return [
            'message' => 'required|string|max:5000',
            'user_id' => 'nullable|exists:users,id',
            'ticket_id' => 'required|exists:tickets,id',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $ticketId = $this->integer('ticket_id');
            $ticket = Ticket::find($ticketId);
            if (!$ticket) {
                return;
            }

            $messageUserId = $this->input('user_id');
            if (!is_null($ticket->user_id) && is_null($messageUserId)) {
                $validator->errors()->add('user_id', 'user_id is required when posting a message to a user-owned ticket.');
            }
        });
    }
}
