<?php

namespace App\Http\Requests\Api\Admin\Tickets;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class UpdateTicketRequest extends AdminApiRequest
{
    protected $permission = 'tickets.update';

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'department' => 'nullable|string|in:' . implode(',', config('settings.ticket_departments', [])),
            'priority' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:open,closed,replied',
        ];
    }
}
