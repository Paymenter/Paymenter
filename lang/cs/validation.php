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

    'accepted' => ':attribute musí být přijat.',
    'active_url' => ':attribute není validní URL.',
    'after' => ':attribute musí být datum po :date.',
    'after_or_equal' => ':attribute musí být datum po nebo roven :date.',
    'alpha' => ':attribute může obsahovat pouze písmena.',
    'alpha_dash' => ':attribute může obsahovat pouze písmena, čísla, pomlčky a podtržítka.',
    'alpha_num' => ':attribute může obsahovat pouze písmena a čísla.',
    'array' => ':attribute musí být pole.',
    'before' => ':attribute musí být datum před :date.',
    'before_or_equal' => ':attribute mmusí být datum před nebo roven :date.',
    'between' => [
        'numeric' => ':attribute musí být mezi :min a :max.',
        'file' => ':attribute musí být mezi :min a :max kilobajty.',
        'string' => ':attribute musí být mezi :min a :max znaky.',
        'array' => ':attribute musí být mezi :min a :max produkty.',
    ],
    'boolean' => ':attribute pole musí být pouze true nebo false.',
    'confirmed' => ':attribute potvrzení se neshoduje.',
    'date' => ':attribute není platný datum.',
    'date_equals' => ':attribute musí být datum roven :date.',
    'date_format' => ':attribute se neshoduje s formátem :format.',
    'different' => ':attribute a :other musí být rozdílný.',
    'digits' => ':attribute musí být :digits místný.',
    'digits_between' => ':attribute musí být mezi :min a :max místy.',
    'dimensions' => ':attribute má neplatné rozlišení obrázku.',
    'distinct' => ':attribute má stejnou hodnotu.',
    'email' => ':attribute musí být platná e-mailová adresa.',
    'ends_with' => ':attribute musí končit jedním z následujících hodnot: :values.',
    'exists' => 'Vybraná :attribute je neplatná.',
    'file' => ':attribute musí být soubor.',
    'filled' => ':attribute musí mít nastavenou hodnotu.',
    'gt' => [
        'numeric' => ':attribute musí být větší než :value.',
        'file' => ':attribute musí být větší než :value kilobajtů.',
        'string' => ':attribute musí být větší než :value znaků.',
        'array' => ':attribute musí mít více než :value položek.',
    ],
    'gte' => [
        'numeric' => ':attribute musí být větší než nebo roven :value.',
        'file' => ':attribute musí být větší než nebo roven :value kilobajtů.',
        'string' => ':attribute musí být větší než nebo roven :value znaků.',
        'array' => ':attribute musí mít :value nebo více položek.',
    ],
    'image' => ':attribute musí být obrázek.',
    'in' => 'vybraná :attribute je neplatná.',
    'in_array' => ':attribute pole se neshoduje s :other.',
    'integer' => ':attribute musí být platný integer.',
    'ip' => ':attribute musí být validní IP adresa.',
    'ipv4' => ':attribute musí být validní IPv4 adresa.',
    'ipv6' => ':attribute musí být validní IPv6 adresa.',
    'json' => ':attribute musí být validní JSON řetězec.',
    'lt' => [
        'numeric' => ':attribute musí být menší než :value.',
        'file' => ':attribute musí být menší než :value kilobajtů.',
        'string' => ':attribute musí být menší než :value znaků.',
        'array' => ':attribute musí mít méně než :value položek.',
    ],
    'lte' => [
        'numeric' => ':attribute musí být menší nebo roven :value.',
        'file' => ':attribute musí být menší nebo roven :value kilobajtů.',
        'string' => ':attribute musí být menší nebo roven :value znaků.',
        'array' => ':attribute nesmí mít více než :value položek.',
    ],
    'max' => [
        'numeric' => ':attribute nesmí být větší než :max.',
        'file' => ':attribute nesmí být větší než :max kilobajtů.',
        'string' => ':attribute nesmí být větší než :max znaků.',
        'array' => ':attribute nesmí mít více než :max položek.',
    ],
    'mimes' => ':attribute musí být soubor typu: :values.',
    'mimetypes' => ':attribute musí být soubor typu: :values.',
    'min' => [
        'numeric' => ':attribute musí být minimálně :min.',
        'file' => ':attribute musí být minimálně :min kilobajtů.',
        'string' => ':attribute musí být minimálně :min znaků.',
        'array' => ':attribute musí mít minimálně :min položek.',
    ],
    'multiple_of' => ':attribute musí být násobek :value.',
    'not_in' => 'Vybraný :attribute je neplatný.',
    'not_regex' => ':attribute format je neplatný.',
    'numeric' => ':attribute musí být číslo.',
    'password' => 'Heslo je nesprávné.',
    'present' => ':attribute musí být přítomný.',
    'regex' => 'Formát :attribute je neplatný.',
    'required' => ':attribute je povinné pole.',
    'required_if' => ':attribute je povinné pole když :other je :value.',
    'required_unless' => ':attribute je povinné pole pokud :other je v :values.',
    'required_with' => ':attribute je povinné pole když :values je přítomný.',
    'required_with_all' => ':attribute je povinné pole když :values jsou přítomné.',
    'required_without' => ':attribute je povinné pole když :values nejsou přítomné.',
    'required_without_all' => ':attribute je povinné pole když nejsou hodnoty :values přítomné.',
    'same' => ':attribute a :other se musí shodovat.',
    'size' => [
        'numeric' => ':attribute musí být :size.',
        'file' => ':attribute musí být :size kilobajtů.',
        'string' => ':attribute musí být :size znaků.',
        'array' => ':attribute musí obsahovat :size položek.',
    ],
    'starts_with' => ':attribute musí začínat jedním z následujících: :values.',
    'string' => ':attribute musí být řetězec znaků.',
    'timezone' => ':attribute musí být platná časová zóna.',
    'unique' => ':attribute se už používá.',
    'uploaded' => ':attribute se nepodařilo nahrát.',
    'url' => ':attribute má neplatný formát.',
    'uuid' => ':attribute musí být platné UUID.',

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
