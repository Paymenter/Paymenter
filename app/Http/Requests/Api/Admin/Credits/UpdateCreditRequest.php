<?php

namespace App\Http\Requests\Api\Admin\Credits;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class UpdateCreditRequest extends AdminApiRequest
{
    protected $permission = 'credits.update';

    public function rules(): array
    {
        return [
            /**
             * @example USD
             */
            'currency_code' => [
                'sometimes',
                'required',
                'string',
                'exists:currencies,code',
                'unique:credits,currency_code,' . $this->route('credit')->id . ',id,user_id,' . $this->route('credit')->user_id,
            ],
            'amount' => 'sometimes|required|numeric|min:0',
        ];
    }
}
