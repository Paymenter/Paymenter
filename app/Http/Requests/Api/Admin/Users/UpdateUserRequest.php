<?php

namespace App\Http\Requests\Api\Admin\Users;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class UpdateUserRequest extends AdminApiRequest
{
    protected $permission = 'users.update';

    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->route()->parameter('user')->id,
            'password' => 'nullable|string|min:8',
            'email_verified_at' => 'nullable|date',
            'role_id' => 'nullable|exists:roles,id',
        ];
    }
}
