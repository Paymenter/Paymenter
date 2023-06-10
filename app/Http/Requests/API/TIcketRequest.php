<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TicketRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required',
            'message' => 'required',
            'priority' => 'required|in:low,medium,high',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message'   => 'The given data was invalid.',
            'data'      => $validator->errors()
        ]));
    }
}