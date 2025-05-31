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
        return $this->user()->tokenCan('admin.' . $this->permission) && $this->user()->hasPermission('admin.' . $this->permission);
    }

    /**
     * Default set of rules to apply to API requests.
     */
    public function rules(): array
    {
        return [];
    }
}
