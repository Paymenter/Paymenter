<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Pole :attribute musi zostać zaakceptowane.',
    'active_url' => 'Pole :attribute nie jest poprawnym adresem URL.',
    'after' => 'Pole :attribute musi być datą po :date.',
    'after_or_equal' => 'Pole :attribute musi być datą po lub równą :date.',
    'alpha' => 'Pole :attribute może zawierać tylko litery.',
    'alpha_dash' => 'Pole :attribute może zawierać tylko litery, cyfry, myślniki i podkreślenia.',
    'alpha_num' => 'Pole :attribute może zawierać tylko litery i cyfry.',
    'array' => 'Pole :attribute musi być tablicą.',
    'before' => 'Pole :attribute musi być datą przed :date.',
    'before_or_equal' => 'Pole :attribute musi być datą przed lub równą :date.',
    'between' => [
        'numeric' => 'Pole :attribute musi mieścić się w przedziale :min - :max.',
        'file' => 'Pole :attribute musi mieścić się w przedziale :min - :max kilobajtów.',
        'string' => 'Pole :attribute musi mieścić się w przedziale :min - :max znaków.',
        'array' => 'Pole :attribute musi zawierać od :min do :max elementów.',
    ],
    'boolean' => 'Pole :attribute musi być wartością logiczną true albo false.',
    'confirmed' => 'Potwierdzenie pola :attribute nie pasuje.',
    'date' => 'Pole :attribute nie jest poprawną datą.',
    'date_equals' => 'Pole :attribute musi być datą równą :date.',
    'date_format' => 'Pole :attribute nie pasuje do formatu :format.',
    'different' => 'Pola :attribute i :other muszą być różne.',
    'digits' => 'Pole :attribute musi mieć :digits cyfr.',
    'digits_between' => 'Pole :attribute musi mieć od :min do :max cyfr.',
    'dimensions' => 'Pole :attribute ma niepoprawne wymiary obrazu.',
    'distinct' => 'Pole :attribute ma zduplikowaną wartość.',
    'email' => 'Pole :attribute musi być poprawnym adresem email.',
    'ends_with' => 'Pole :attribute musi kończyć się jednym z następujących: :values.',
    'exists' => 'Wybrany :attribute jest niepoprawny.',
    'file' => 'Pole :attribute musi być plikiem.',
    'filled' => 'Pole :attribute musi mieć wartość.',
    'gt' => [
        'numeric' => 'Pole :attribute musi być większe niż :value.',
        'file' => 'Pole :attribute musi być większe niż :value kilobajtów.',
        'string' => 'Pole :attribute musi być dłuższe niż :value znaków.',
        'array' => 'Pole :attribute musi zawierać więcej niż :value elementów.',
    ],
    'gte' => [
        'numeric' => 'Pole :attribute musi być większe lub równe :value.',
        'file' => 'Pole :attribute musi być większe lub równe :value kilobajtów.',
        'string' => 'Pole :attribute musi być dłuższe lub równe :value znaków.',
        'array' => 'Pole :attribute musi zawierać :value elementów lub więcej.',
    ],
    'image' => 'Pole :attribute musi być obrazem.',
    'in' => 'Wybrany :attribute jest niepoprawny.',
    'in_array' => 'Pole :attribute nie istnieje w :other.',
    'integer' => 'Pole :attribute musi być liczbą całkowitą.',
    'ip' => 'Pole :attribute musi być poprawnym adresem IP.',
    'ipv4' => 'Pole :attribute musi być poprawnym adresem IPv4.',
    'ipv6' => 'Pole :attribute musi być poprawnym adresem IPv6.',
    'json' => 'Pole :attribute musi być poprawnym łańcuchem JSON.',
    'lt' => [
        'numeric' => 'Pole :attribute musi być mniejsze niż :value.',
        'file' => 'Pole :attribute musi być mniejsze niż :value kilobajtów.',
        'string' => 'Pole :attribute musi być krótsze niż :value znaków.',
        'array' => 'Pole :attribute musi zawierać mniej niż :value elementów.',
    ],
    'lte' => [
        'numeric' => 'Pole :attribute musi być mniejsze lub równe :value.',
        'file' => 'Pole :attribute musi być mniejsze lub równe :value kilobajtów.',
        'string' => 'Pole :attribute musi być krótsze lub równe :value znaków.',
        'array' => 'Pole :attribute nie może zawierać więcej niż :value elementów.',
    ],
    'max' => [
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
        'file' => 'Pole :attribute nie może być większe niż :max kilobajtów.',
        'string' => 'Pole :attribute nie może być dłuższe niż :max znaków.',
        'array' => 'Pole :attribute nie może zawierać więcej niż :max elementów.',
    ],
    'mimes' => 'Pole :attribute musi być plikiem typu: :values.',
    'mimetypes' => 'Pole :attribute musi być plikiem typu: :values.',
    'min' => [
        'numeric' => 'Pole :attribute musi mieć co najmniej :min.',
        'file' => 'Pole :attribute musi mieć co najmniej :min kilobajtów.',
        'string' => 'Pole :attribute musi mieć co najmniej :min znaków.',
        'array' => 'Pole :attribute musi zawierać co najmniej :min elementów.',
    ],
    'multiple_of' => 'Pole :attribute musi być wielokrotnością :value.',
    'not_in' => 'Wybrany :attribute jest niepoprawny.',
    'not_regex' => 'Format pola :attribute jest niepoprawny.',
    'numeric' => 'Pole :attribute musi być liczbą.',
    'password' => 'Hasło jest niepoprawne.',
    'present' => 'Pole :attribute musi być obecne.',
    'regex' => 'Format pola :attribute jest niepoprawny.',
    'required' => 'Pole :attribute jest wymagane.',
    'required_if' => 'Pole :attribute jest wymagane gdy :other jest :value.',
    'required_unless' => 'Pole :attribute jest wymagane chyba że :other znajduje się w :values.',
    'required_with' => 'Pole :attribute jest wymagane gdy :values jest obecne.',
    'required_with_all' => 'Pole :attribute jest wymagane gdy :values są obecne.',
    'required_without' => 'Pole :attribute jest wymagane gdy :values nie jest obecne.',
    'required_without_all' => 'Pole :attribute jest wymagane gdy żadne z :values nie są obecne.',
    'same' => 'Pola :attribute i :other muszą być identyczne.',
    'size' => [
        'numeric' => 'Pole :attribute musi mieć :size.',
        'file' => 'Pole :attribute musi mieć :size kilobajtów.',
        'string' => 'Pole :attribute musi mieć :size znaków.',
        'array' => 'Pole :attribute musi zawierać :size elementów.',
    ],
    'starts_with' => 'Pole :attribute musi zaczynać się jednym z następujących: :values.',
    'string' => 'Pole :attribute musi być łańcuchem znaków.',
    'timezone' => 'Pole :attribute musi być poprawną strefą czasową.',
    'unique' => 'Pole :attribute już istnieje.',
    'uploaded' => 'Pole :attribute nie udało się przesłać.',
    'url' => 'Format pola :attribute jest niepoprawny.',
    'uuid' => 'Pole :attribute musi być poprawnym identyfikatorem UUID.',


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],
];
