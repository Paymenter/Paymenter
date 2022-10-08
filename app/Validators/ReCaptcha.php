<?php

namespace App\Validators;

use GuzzleHttp\Client;
use App\Models\Settings;
class ReCaptcha
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        if(Settings::first()->recaptcha == 0) {
            return true;
        }
        $client = new Client;
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' =>
                [
                    'secret' => Settings::first()->recaptcha_secret_key,
                    'response' => $value
                ]
            ]
        );
        $body = json_decode((string)$response->getBody());
        return $body->success;
    }
}
