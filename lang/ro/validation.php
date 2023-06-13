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

    'accepted' => 'Campul :attribute trebuie acceptat.',
    'active_url' => 'Campul :attribute nu este o adresă URL validă.',
    'after' => 'Campul :attribute trebuie să fie o dată după :date.',
    'after_or_equal' => 'Campul :attribute trebuie să fie o dată ulterioară sau egală cu :date.',
    'alpha' => 'Campul :attribute poate conține doar litere.',
    'alpha_dash' => 'Campul :attribute poate conține doar litere, cifre, liniuțe și liniuțe de subliniere.',
    'alpha_num' => 'Campul :attribute poate conține doar litere și cifre.',
    'array' => 'Campul :attribute trebuie să fie o matrice.',
    'before' => 'Campul :attribute trebuie să fie o dată înainte de :date.',
    'before_or_equal' => 'Campul :attribute trebuie să fie o dată anterioară sau egală cu :date.',
    'between' => [
        'numeric' => 'Campul :attribute trebuie să fie între :min și :max.',
        'file' => 'Campul :attribute trebuie să fie între :min și :max kilobytes.',
        'string' => 'Campul :attribute trebuie să aibă între caractere :min și :max.',
        'array' => 'Campul :attribute trebuie să aibă între :min și :max articole.',
    ],
    'boolean' => 'Campul :attribute trebuie să fie adevărat sau fals.',
    'confirmed' => 'Campul :attribute confirmarea nu se potrivește.',
    'date' => 'Campul :attribute nu este o dată validă.',
    'date_equals' => 'Campul :attribute trebuie să fie o dată egală cu :date.',
    'date_format' => 'Campul :attribute nu se potrivește cu formatul :format.',
    'different' => 'Campul :attribute și :other trebuie să fie diferite.',
    'digits' => 'Campul :attribute trebuie să fie :digits cifre.',
    'digits_between' => 'Campul :attribute trebuie să fie între cifrele :min și :max.',
    'dimensions' => 'Campul :attribute are dimensiuni nevalide ale imaginii.',
    'distinct' => 'Campul :attribute are o valoare duplicat.',
    'email' => 'Campul :attribute trebuie să fie o adresă de e-mail validă.',
    'ends_with' => 'Campul :attribute trebuie să se încheie cu una dintre următoarele: :values.',
    'exists' => 'Campul selectat :attribute este invalid.',
    'file' => 'Campul :attribute trebuie să fie un fișier.',
    'filled' => 'Campul :attribute trebuie să aibă o valoare.',
    'gt' => [
        'numeric' => 'Campul :attribute trebuie să fie mai mare decât :value.',
        'file' => 'Campul :attribute trebuie să fie mai mare decât :value kilobytes.',
        'string' => 'Campul :attribute trebuie să fie mai mare decât caracterele :value.',
        'array' => 'Campul :attribute trebuie să aibă mai multe elemente decât :value.',
    ],
    'gte' => [
        'numeric' => 'Campul :attribute trebuie să fie mai mare sau egal cu :value.',
        'file' => 'Campul :attribute trebuie să fie mai mare sau egal cu :value kilobytes.',
        'string' => 'Campul :attribute trebuie să fie mai mare sau egal cu caracterele :value.',
        'array' => 'Campul :attribute trebuie să aibă: elemente de valoare sau mai multe.',
    ],
    'image' => 'Campul :attribute trebuie să fie o imagine.',
    'in' => 'Campul selected :attribute este invalid.',
    'in_array' => 'Campul :attribute nu există în :other.',
    'integer' => 'Campul :attribute trebuie să fie un număr întreg.',
    'ip' => 'Campul :attribute trebuie să fie o adresă IP validă.',
    'ipv4' => 'Campul :attributetrebuie să fie o adresă IPv4 validă.',
    'ipv6' => 'Campul :attribute trebuie să fie o adresă IPv4 validă.',
    'json' => 'Campul :attribute trebuie să fie un șir JSON valid.',
    'lt' => [
        'numeric' => 'Campul :attributetrebuie să fie mai mic decât :value.',
        'file' => 'Campul :attribute trebuie să fie mai mic decât :value kilobytes.',
        'string' => 'Campul :attribute trebuie să fie mai mic decât caracterele :value.',
        'array' => 'Campul :attribute trebuie să aibă mai puțin de elemente :value.',
    ],
    'lte' => [
        'numeric' => 'Campul :attribute trebuie să fie mai mic sau egal cu :value.',
        'file' => 'Campul :attribute trebuie să fie mai mic sau egal :value kilobytes.',
        'string' => 'Campul :attribute trebuie să fie mai mic sau egal cu caractere :value.',
        'array' => 'Campul :attribute nu trebuie să aibă mai mult de elemente :value.',
    ],
    'max' => [
        'numeric' => 'Campul :attribute nu poate fi mai mare decât :max.',
        'file' => 'Campul :attribute nu poate fi mai mare de :max kilobytes.',
        'string' => 'Campul :attribute nu poate fi mai mare decât :max caractere.',
        'array' => 'Campul :attribute nu poate avea mai mult de :max articole.',
    ],
    'mimes' => 'Campul :attribute trebuie să fie un fișier de tip: :values.',
    'mimetypes' => 'Campul :attribute trebuie să fie un fișier de tip: :values.',
    'min' => [
        'numeric' => 'Campul :attribute trebuie sa fie cel putin :min.',
        'file' => 'Campul :attribute trebuie să fie de cel puțin :min kilobytes.',
        'string' => 'Campul :attribute trebuie să conțină cel puțin :min caractere.',
        'array' => 'Campul :attribute trebuie să aibă cel puțin :min articole.',
    ],
    'multiple_of' => 'Campul :attribute trebuie să fie un multiplu al :value.',
    'not_in' => 'Campul selected :attribute este invalid.',
    'not_regex' => 'Campul :attribute formatul este invalid.',
    'numeric' => 'Campul :attribute trebuie sa fie un numar.',
    'password' => 'Campul parola este incorectă.',
    'present' => 'Campul :attribute câmpul trebuie să fie prezent.',
    'regex' => 'Campul :attribute formatul este invalid.',
    'required' => 'Campul :attribute câmpul este obligatoriu.',
    'required_if' => 'Campul :attribute câmpul este obligatoriu când :other este :value.',
    'required_unless' => 'Campul :attribute câmpul este obligatoriu, cu excepția cazului în care :other este în :values.',
    'required_with' => 'Campul :attribute câmpul este obligatoriu când :values ​​este prezent.',
    'required_with_all' => 'Campul :attribute câmpul este obligatoriu când sunt prezente valorile.',
    'required_without' => 'Campul :attribute câmpul este obligatoriu când :values ​​nu este prezent.',
    'required_without_all' => 'Campul :attribute câmpul este obligatoriu când niciuna dintre valorile : nu este prezentă.',
    'same' => 'Campul :attribute și :other trebuie să se potrivească.',
    'size' => [
        'numeric' => 'Campul :attribute trebuie să fie :size.',
        'file' => 'Campul :attribute trebuie să fie :size kilobytes.',
        'string' => 'Campul :attribute trebuie să fie :size characters.',
        'array' => 'Campul :attribute trebuie sa contina :size items.',
    ],
    'starts_with' => 'Campul :attribute trebuie să înceapă cu una dintre următoarele: :values.',
    'string' => 'Campul :attribute trebuie să fie un șir.',
    'timezone' => 'Campul :attribute trebuie să fie o zonă validă.',
    'unique' => 'Campul :attribute a fost deja luat.',
    'uploaded' => 'Campul :attribute nu s-a putut încărca.',
    'url' => 'Campul :attribute formatul este invalid.',
    'uuid' => 'Campul :attribute trebuie să fie un UUID valid.',

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
