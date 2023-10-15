<?php

namespace App\Validators;


class Domain
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        return preg_match('/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i', $value);
    }

    public function message($message, $attribute, $rule, $parameters)
    {
        return 'The ' . $attribute . ' field must be a valid domain.';
    }
}
