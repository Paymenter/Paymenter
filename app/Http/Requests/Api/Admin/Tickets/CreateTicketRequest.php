<?php

namespace App\Http\Requests\Api\Admin\Tickets;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class CreateTicketRequest extends AdminApiRequest
{
    protected $permission = 'tickets.create';

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

    public function prepareForValidation()
    {
        $this->mergeIfMissing([
            'priority' => 'medium', // Default priority to 'medium' if not provided
            'status' => 'open', // Default status to 'open' if not provided
        ]);
    }
}
