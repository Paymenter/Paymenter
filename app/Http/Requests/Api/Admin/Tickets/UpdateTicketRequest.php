<?php

namespace App\Http\Requests\Api\Admin\Tickets;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class UpdateTicketRequest extends AdminApiRequest
{
    protected $permission = 'tickets.update';

    public function rules(): array
    {
        return [
            'subject' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|required|exists:users,id',
            'department' => 'sometimes|nullable|string|in:' . implode(',', config('settings.ticket_departments', [])),
            'priority' => 'sometimes|required|string|in:low,medium,high',
            'status' => 'sometimes|required|string|in:open,closed,replied',
        ];
    }
}
