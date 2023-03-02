<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | A(z) following language lines contain A(z) default error messages used by
    | A(z) validator class. Some of A(z)se rules have multiple versions such
    | as A(z) size rules. Feel free to tweak each of A(z)se messages here.
    |
    */

    'accepted' => 'A(z) :attribute elfogadása kötelező.',
    'active_url' => 'A(z) :attribute nem megfelelő URL.',
    'after' => 'A(z) :attribute dátumnak kell lenni az után :date.',
    'after_or_equal' => 'A(z) :attribute dátumnak kell lenni vagy azzal azonosal kell lennie :date.',
    'alpha' => 'A(z) :attribute csak betűket tartalmazhat.',
    'alpha_dash' => 'A(z) :attribute csak számokat, betűket, speciális karaktereket és alsóvonalakat tartalmazhat',
    'alpha_num' => 'A(z) :attribute csak számokat és betűket tartalmazhat',
    'array' => 'A(z) :attribute tömbnek kell lennie.',
    'before' => 'A(z) :attribute előtt dátumnak kell lennie :date.',
    'before_or_equal' => 'A(z) :attribute dátumnak kell lennie vagy azzal azonosnak kell lennie :date.',
    'between' => [
        'numeric' => 'A(z) :attribute között :min és :max között kell lennie.',
        'file' => 'A(z) :attribute között :min és :max kilobájt között kell lennie.',
        'string' => 'A(z) :attribute között :min és :max karakter között kell lennie.',
        'array' => 'A(z) :attribute között :min és :max és között kell lennie a tételeknek',
    ],
    'boolean' => 'A(z) :attribute mezőnek igaz vagy hamisnak kell lennie',
    'confirmed' => 'A(z) :attribute visszaigazolása nem egyezik',
    'date' => 'A(z) :attribute nem megfelelő dátum',
    'date_equals' => 'A(z) :attribute dátumnak vagy azzal azonosnak kell lennie :date.',
    'date_format' => 'A(z) :attribute nem egyezik a(z) formátummal :format.',
    'different' => 'A(z) :attribute és :oA(z)r másnak kell lennie.',
    'digits' => 'A(z) :attribute számoknak :digits kell lennie',
    'digits_between' => 'A(z) :attribute között :min és :max számok között kell lennie',
    'dimensions' => 'A(z) :attribute érvénytelen dimenziói vannak',
    'distinct' => 'A(z) :attribute duplikált értéke van',
    'email' => 'A(z) :attribute valós email cimnek kell lennie',
    'ends_with' => 'A(z) :attribute végzőséssel kell rendelkeznie a(z) following: :values.',
    'exists' => 'A(z) kiválasztott :attribute érvénytelen',
    'file' => 'A(z) :attribute fájlnak kell lennie',
    'filled' => 'A(z) :attribute mezőben szerepelnie kell egy értéknek.',
    'gt' => [
        'numeric' => 'A(z) :attribute nagyobbnak kell lennie :value.',
        'file' => 'A(z) :attribute nagyobbnak kell lennie :value kilobytes.',
        'string' => 'A(z) :attribute nagyobnnak kell lennie :value characters.',
        'array' => 'A(z) :attribute nagyobbnak kell lennie :value items.',
    ],
    'gte' => [
        'numeric' => 'A(z) :attribute nagyobbnak vagy egyenlőnek kell lennie :value.',
        'file' => 'A(z) :attribute nagyobbnak vagy egyenlőnek kell lennie :value kilobytes.',
        'string' => 'A(z) :attribute nagyobbnak vagy egyenlőnek kell lennie :value characters.',
        'array' => 'A(z) :attribute kell lennie legalább :value tételnek vagy többnek.',
    ],
    'image' => 'A(z) :attribute képnek kell lennie.',
    'in' => 'A(z) kiválasztott :attribute érvénytelen',
    'in_array' => 'A(z) :attribute mező nem található a(z) :oA(z)r.',
    'integer' => 'A(z) :attribute egésznek kell lennie.',
    'ip' => 'A(z) :attribute valós IP cimnek kell lennie',
    'ipv4' => 'A(z) :attribute valós IPv4 cimnek kell lennie',
    'ipv6' => 'A(z) :attribute valós IPv6 cimnek kell lennie',
    'json' => 'A(z) :attributevalós JSON stringnek kell lennie',
    'lt' => [
        'numeric' => 'A(z) :attribute kevesebbnek kell lennie mint :value.',
        'file' => 'A(z) :attribute kevesebbnek kell lennie mint :value kilobájt',
        'string' => 'A(z) :attribute kevesebbnek kell lennie mint :value karakter.',
        'array' => 'A(z) :attribute must have less than :value tétel.',
    ],
    'lte' => [
        'numeric' => 'A(z) :attribute kevesebbnek kell lennie vagy egyenlőnek :value.',
        'file' => 'A(z) :attribute kevesebbnek kell lennie vagy egyenlőnek :value kilobytes.',
        'string' => 'A(z) :attribute kevesebbnek kell lennie vagy egyenlőnek :value characters.',
        'array' => 'A(z) :attribute többnek kell lennie mint :value tétel.',
    ],
    'max' => [
        'numeric' => 'A(z) :attributenem lehet nagyobb mint :max.',
        'file' => 'A(z) :attributenem lehet nagyobb mint :max kilobájt.',
        'string' => 'A(z) :attributenem lehet nagyobb mint :max karakter.',
        'array' => 'A(z) :attribute may not have more than :max tétel.',
    ],
    'mimes' => 'A(z) :attributefájltipusnak kell lennie: :values.',
    'mimetypes' => 'A(z) :attributefájltipusnak kell lennie: :values.',
    'min' => [
        'numeric' => 'A(z) :attribute legalább ennyinek kell lennie :min.',
        'file' => 'A(z) :attributelegalább ennyinek kell lennie :min kilobytes.',
        'string' => 'A(z) :attributelegalább ennyinek kell lennie :min characters.',
        'array' => 'A(z) :attribute legal8bb ennyinek kell lennie :min items.',
    ],
    'multiple_of' => 'A(z) :attribute többszörösének kell lennie :value.',
    'not_in' => 'A(z) kiválasztott :attribute érvénytelen.',
    'not_regex' => 'A(z) :attribute formátum érvénytelen.',
    'numeric' => 'A(z) :attribute számnak kell lennie .',
    'password' => 'A(z) jelszó érvénytelen.',
    'present' => 'A(z) :attribute mezőnek jelen kell lennie.',
    'regex' => 'A(z) :attribute formátum érvénytelen.',
    'required' => 'A(z) :attribute mező kitöltése szükséges.',
    'required_if' => 'A(z) :attribute szükséges ha :oA(z)r is :value.',
    'required_unless' => 'A(z) :attribute mezőre szükség van ha :oA(z)r is in :values.',
    'required_with' => 'A(z) :attribute szükséges ha :values jelen van.',
    'required_with_all' => 'A(z) :attribute szükséges ha :values érték megadva',
    'required_without' => 'A(z) :attribute szükséges ha :values érték nincs megadva.',
    'required_without_all' => 'A(z) :attribute szükséges ha none of :values értékek megvannak adva.',
    'same' => 'A(z) :attribute és :oA(z)r egyeznie kell',
    'size' => [
        'numeric' => 'A(z) :attribute kell lennie.',
        'file' => 'A(z) :attribute  kilobájt méretűnek kell lennie.',
        'string' => 'A(z) :attribute karakter hosszúnak kell lennie.',
        'array' => 'A(z) :attribute tartalmaznia kell :size tétel.',
    ],
    'starts_with' => 'A(z) :attribute kezdődnie kell legalább a(z) következővel following: :values.',
    'string' => 'A(z) :attribute stringnek kell lennie.',
    'timezone' => 'A(z) :attribute érvényes zónának kell lennie.',
    'unique' => 'A(z) :attribute már használatban van.',
    'uploaded' => 'A(z) :attribute feltöltése sikertelen.',
    'url' => 'A(z) :attribute formátum érvénytelen.',
    'uuid' => 'A(z) :attribute érvényes UUID-nak kell lennie ',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using A(z)
    | convention "attribute.rule" to name A(z) lines. This makes it quick to
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
    | A(z) following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],
];
