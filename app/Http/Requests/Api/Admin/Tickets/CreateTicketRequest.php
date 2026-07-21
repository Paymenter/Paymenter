<?php

namespace App\Http\Requests\Api\Admin\Tickets;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use Illuminate\Validation\Validator;

class CreateTicketRequest extends AdminApiRequest
{
    protected $permission = 'tickets.create';

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'guest_name' => 'nullable|string|max:255',
            'guest_email' => 'nullable|email|max:255',
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $hasUserId = !is_null($this->input('user_id'));
            $hasGuestEmail = filled($this->input('guest_email'));
            $hasGuestName = filled($this->input('guest_name'));

            if (!$hasUserId && !$hasGuestEmail) {
                $validator->errors()->add('user_id', 'Either user_id or guest_email is required.');
            }

            if ($hasUserId && ($hasGuestEmail || $hasGuestName)) {
                $validator->errors()->add('guest_email', 'Guest fields cannot be used when user_id is set.');
            }

            if ($hasGuestName && !$hasGuestEmail) {
                $validator->errors()->add('guest_email', 'guest_email is required when guest_name is provided.');
            }
        });
    }
}
