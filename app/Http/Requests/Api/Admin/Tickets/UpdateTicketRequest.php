<?php

namespace App\Http\Requests\Api\Admin\Tickets;

use App\Http\Requests\Api\Admin\AdminApiRequest;
use Illuminate\Validation\Validator;

class UpdateTicketRequest extends AdminApiRequest
{
    protected $permission = 'tickets.update';

    public function rules(): array
    {
        return [
            'subject' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|nullable|exists:users,id',
            'guest_name' => 'sometimes|nullable|string|max:255',
            'guest_email' => 'sometimes|nullable|email|max:255',
            'department' => 'sometimes|nullable|string|in:' . implode(',', config('settings.ticket_departments', [])),
            'priority' => 'sometimes|required|string|in:low,medium,high',
            'status' => 'sometimes|required|string|in:open,closed,replied',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $payloadContainsIdentityField = $this->hasAny(['user_id', 'guest_email', 'guest_name']);
            if (!$payloadContainsIdentityField) {
                return;
            }

            $hasUserId = !is_null($this->input('user_id'));
            $hasGuestEmail = filled($this->input('guest_email'));
            $hasGuestName = filled($this->input('guest_name'));

            if (!$hasUserId && !$hasGuestEmail) {
                $validator->errors()->add('user_id', 'Either user_id or guest_email is required when updating ticket ownership.');
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
