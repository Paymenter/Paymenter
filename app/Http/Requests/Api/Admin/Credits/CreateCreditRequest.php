<?php

namespace App\Http\Requests\Api\Admin\Credits;

use App\Http\Requests\Api\Admin\AdminApiRequest;

class CreateCreditRequest extends AdminApiRequest
{
    protected $permission = 'credits.create';

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            /**
             * @example USD
             */
            'currency_code' => [
                'required',
                'string',
                'exists:currencies,code',
                'unique:credits,currency_code,NULL,id,user_id,' . $this->input('user_id'),
            ],
            'amount' => 'required|numeric|min:0',
        ];
    }
}
