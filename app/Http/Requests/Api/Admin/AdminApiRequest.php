<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

abstract class AdminApiRequest extends FormRequest
{
    protected $permission;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array('admin.' . $this->permission, $this->instance()->attributes->get('api_key_permissions', []));
    }

    /**
     * Default set of rules to apply to API requests.
     */
    public function rules(): array
    {
        return [];
    }
}
