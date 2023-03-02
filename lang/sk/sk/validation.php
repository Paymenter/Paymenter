<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    |  following language lines contain  default error messages used by
    |  validator class. Some of se rules have multiple versions such
    | as  size rules. Feel free to tweak each of se messages here.
    |
    */

    'accepted' => ':attribute prijaťie je nutná',
    'active_url' => ' :attribute zlý URL',
    'after' => ' :attribute musí mať len dátum po :date.',
    'after_or_equal' => ' :attribute musímať dátum alebo rovnaká :date.',
    'alpha' => ' :attribute môže obsahovať len list.',
    'alpha_dash' => ' :attribute musí obsahovať len alfanumerické a špecialné karaktery',
    'alpha_num' => ' :attribute musí obsahovač len alfanumerické karaktery',
    'array' => ' :attribute musí mať blok - array.',
    'before' => ' :attribute musí mať dátum pred :date.',
    'before_or_equal' => ' :attribute musí mať dátum pred alebo rovnaká :date.',
    'between' => [
        'numeric' => ' :attribute medzi :min a :max musí byť',
        'file' => ' :attribute medzi :min a :max musí byť kilobajtov',
        'string' => ' :attribute medzi :min a :max musí byť karakterov.',
        'array' => ' :attribute medzi :min a :max musí byt produkty',
    ],
    'boolean' => ' :attribute pole musí byť len ano alebo nie',
    'confirmed' => ' :attribute potvrednie sa nezhoduje',
    'date' => ' :attribute nesprávný dátum',
    'date_equals' => ' :attribute musí byť len dátum :date.',
    'date_format' => ' :attribute nezhoduje s povoleným formátom :format.',
    'different' => ' :attribute a :or musí byť iný',
    'digits' => ' :attribute musí byť výrazne len :digits čísle ',
    'digits_between' => ' :attribute  :min a :max musí byť medzi muí byť ',
    'dimensions' => ' :attribute nesprávné dimenziá',
    'distinct' => ' :attribute duplikovaná hodnota',
    'email' => ' :attribute musí byť správná emailová adresa',
    'ends_with' => ' :attribute musí končiť s following: :values.',
    'exists' => ' zvolená :attribute nesprávná',
    'file' => ' :attribute musí byť súbor',
    'filled' => ' :attribute pole musí obsahovať aspoń jeden hodnota.',
    'gt' => [
        'numeric' => ' :attribute musí byť večšia.',
        'file' => ' :attribute musí byť večľia ako :value kilobytes.',
        'string' => ' :attribute musí byť večšia ako :value karakterov.',
        'array' => ' :attribute musí byť večšia ako :value položka.',
    ],
    'gte' => [
        'numeric' => ' :attribute musí byť večšia alebo rovnaká ako :value.',
        'file' => ' :attribute musí byť večšia alebo rovnaká ako :value kilobytes.',
        'string' => ' :attribute musí byť večšia alebo rovnaká ako  :value characters.',
        'array' => ' :attribute musí obsahovať aspon :value jeden položku.',
    ],
    'image' => ' :attribute musí byť obrázok.',
    'in' => 'zvolená :attribute neplatná',
    'in_array' => ' :attribute pola nedajdená alebo :or.',
    'integer' => ' :attribute musí byt integer',
    'ip' => ' :attribute musí byť správná IP adresa',
    'ipv4' => ' :attribute musí byť správná IPv4 adresa',
    'ipv6' => ' :attribute musíé byť správná IPv6 adresa',
    'json' => ' :attribute Musí mať správne formatovaná JSON',
    'lt' => [
        'numeric' => ':attribute musí byť menší ako :value.',
        'file' => ':a musí byť menší ako :value kilobajty',
        'string' => ':attribute musí byť menší ako :value znak.',
        'array' => ':attribute musí mať menej ako :value položka',
    ],
    'lte' => [
        'numeric' => ' :attribute musí byť menší alebo rovný :value.',
        'file' => ' :attribute musí byť menší alebo rovný :value kilobajty.',
        'string' => ' :attribute musí byť menší alebo rovný :value znakov',
        'array' => ':attribute musí byť väčší ako :value položka.',
    ],
    'max' => [
        'numeric' => ' :attribute môže byť väčší ako :max.',
        'file' => ' :attribute môže byť väčší ako :max kilobajtov.',
        'string' => ':attribute môže byť väčší ako :max znak.',
        'array' => ':attribute nesmie mať viac ako :max prvok.',
    ],
    'mimes' => ' musí byť :attributefiletype: :values.',
    'mimetypes' => ' :attribute file-type musí byť: :values.',
    'min' => [
        'numeric' => ' :attribute musí byť aspoň :min.',
        'file' => ' :attribute musí byť aspoň :min kilobajtov.',
        'string' => ' :attribute musí mať aspoň :min znakov',
        'array' => ' :attribute musí mať aspoň :min položiek',
    ],
    'multiple_of' => ' :attribute musí byť násobkom :value.',
    'not_in' => ' vybraný :attribute neplatný.',
    'not_regex' => ' :attribute format invalid',
    'numeric' => ' :attribute musí byť číslo' ,
    'password' => ' heslo neplatné.',
    'present' => ' :attribute musí byť prítomný.',
    'regex' => ' :attribute neplatný formát.',
    'required' => ' :pole atribútu povinné.',
    'required_if' => ' :attribute povinný, ak :or je :value.',
    'required_unless' => ' :pole atribútu povinné, ak :or je v :values',
    'required_with' => ' :attribute povinný, ak je prítomný :values',
    'required_with_all' => ' :attribute povinný, ak je uvedená hodnota :values',
    'required_without' => ' :attribute povinný, ak hodnota :values nie je uvedená',
    'required_without_all' => ' :attribute povinný, ak nie je uvedená žiadna z :values',
    'same' => ' :attribute a :or musia byť rovnaké',
    'size' => [
        'numeric' => ' :attribute musí byť.',
        'file' => ' :attribute musí mať veľkosť kilobyte.',
        'string' => ' :attribute musí byť dlhý ako znak.',
        'array' => ' :attribute musí obsahovať položku :size.',
    ],
    'starts_with' => ' :attribute musí začínať aspoň nasledujúcim: :values.',
    'string' => ' :attribute musí byť reťazec.',
    'timezone' => ' :attribute musí byť platná zóna.',
    'unique' => ' :attribute sa už používa.',
    'uploaded' => ' :attribute sa nepodarilo nahrať.',
    'url' => ' :attribute má neplatný formát.',
    'uuid' => ' :attribute musí byť správná UUID ',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using 
    | convention "attribute.rule" to name  lines. This makes it quick to
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
    |  following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],
];
