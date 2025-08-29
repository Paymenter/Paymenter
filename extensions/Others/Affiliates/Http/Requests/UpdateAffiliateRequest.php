<?php

namespace Paymenter\Extensions\Others\Affiliates\Http\Requests;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class UpdateAffiliateRequest extends AdminApiRequest
{
    protected $permission = 'affiliates.update';

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'code' => 'nullable|string|max:255|unique:affiliates,code,' . $this->route()->parameter('affiliate')->id,
            'enabled' => 'nullable|boolean',
            'reward' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
