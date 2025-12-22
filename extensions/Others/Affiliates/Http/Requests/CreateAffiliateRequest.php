<?php

namespace Paymenter\Extensions\Others\Affiliates\Http\Requests;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class CreateAffiliateRequest extends AdminApiRequest
{
    protected $permission = 'affiliates.create';

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|max:255|unique:Paymenter\Extensions\Others\Affiliates\Models\Affiliate,code',
            'enabled' => 'nullable|boolean',
            'reward' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
