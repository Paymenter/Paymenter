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

    'accepted' => ':attribute skal accepteres.',
    'accepted_if' => ':attribute skal accepteres, når :other er :value.',
    'active_url' => ':attribute er ikke en gyldig URL.',
    'after' => ':attribute skal være en dato efter :date.',
    'after_or_equal' => ':attribute skal være en dato efter eller lig med :date.',
    'alpha' => ':attribute må kun bestå af bogstaver.',
    'alpha_dash' => ':attribute må kun indeholde bogstaver, tal og bindestreger.',
    'alpha_num' => ':attribute må kun indeholde bogstaver og tal.',
    'array' => ':attribute skal være et array.',
    'ascii' => ':attribute skal kun indeholde alpanumeriske tegn og symboler.',
    'before' => ':attribute skal være en dato før :date.',
    'before_or_equal' => ':attribute skal være en dato før eller lig med :date.',
    'between' => [
        'array' => ':attribute skal indeholde mellem :min og :max elementer.',
        'file' => ':attribute skal være mellem :min og :max kilobyte.',
        'numeric' => ':attribute skal være mellem :min og :max.',
        'string' => ':attribute skal være mellem :min og :max tegn.',
    ],
    'boolean' => ':attribute feltet skal være enten true eller false.',
    'can' => ':attribute indeholder en uautoriseret værdi.',
    'confirmed' => ':attribute bekræftelsen matcher ikke.',
    'current_password' => 'Adgangskoden er forkert.',
    'date' => ':attribute er ikke en gyldig dato.',
    'date_equals' => ':attribute skal være en dato lig med :date.',
    'date_format' => ':attribute matcher ikke formatet :format.',
    'decimal' => ':attribute skal have :decimal decimalpladser.',
    'declined' => 'De :attribute skal afvises.',
    'declined_if' => ':attribute skal afvises, når :other er :value.',
    'different' => ':attribute og :other skal være forskellige.',
    'digits' => ':attribute skal have :digits cifre.',
    'digits_between' => ':attribute skal have mellem :min og :max cifre.',
    'dimensions' => ':attribute har ugyldige billedmål.',
    'distinct' => ':attribute feltet har en duplikeret værdi.',
    'doesnt_end_with' => ':attribute må ikke ende med en af følgende: :values.',
    'doesnt_start_with' => ':attribute må ikke starte med en af følgende: :values.',
    'email' => ':attribute skal være en gyldig e-mail-adresse.',
    'ends_with' => ':attribute skal ende med en af følgende: :values.',
    'enum' => 'Valgte :attribute er ugyldig.',
    'exists' => 'Valgte :attribute er ugyldig.',
    'extensions' => 'Feltet :attribute skal have en af ​​følgende udvidelser: :values.',
    'file' => ':attribute skal være en fil.',
    'filled' => ':attribute feltet skal have en værdi.',
    'gt' => [
        'array' => ':attribute skal have mere end :value elementer.',
        'file' => ':attribute skal være større end :value kilobytes.',
        'numeric' => ':attribute skal være større end :value.',
        'string' => ':attribute skal være mere end :value tegn.',
    ],
    'gte' => [
        'array' => ':attribute skal have :value elementer eller mere.',
        'file' => ':attribute skal være større end eller lig med :value kilobytes.',
        'numeric' => ':attribute skal være større end eller lig med :value.',
        'string' => ':attribute skal være mere end eller lig med :value tegn.',
    ],
    'hex_color' => 'Feltet :attribute skal være en gyldig hexadecimal farve.',
    'image' => ':attribute skal være et billede.',
    'in' => 'Den valgte :attribute er ugyldig.',
    'in_array' => ':attribute feltet findes ikke i :other.',
    'integer' => ':attribute skal være et heltal.',
    'ip' => ':Attribute skal være en gyldig IP adresse.',
    'ipv4' => ':attribute skal være en gyldig IPv4-adresse.',
    'ipv6' => ':attribute skal være en gyldig IPv6-adresse.',
    'json' => ':attribute skal være en gyldig JSON streng.',
    'list' => ':attribute skal være en liste.',
    'lowercase' => ':attribute skal være i små bogstaver.',
    'lt' => [
        'array' => ':attribute skal have mindre end :value elementer.',
        'file' => ':attribute skal være mindre end :value kilobyte.',
        'numeric' => ':attribute skal være mindre end :value.',
        'string' => ':attribute skal være mindre end :value tegn.',
    ],
    'lte' => [
        'array' => ':attribute må ikke have mere end :value elementer.',
        'file' => ':attribute skal være mindre end eller lig med :value kilobyte.',
        'numeric' => ':attribute skal være mindre end eller lig med :value.',
        'string' => ':attribute skal være mindre end eller lig med :value tegn.',
    ],
    'mac_address' => ':attribute skal være en gyldig MAC-adresse.',
    'max' => [
        'array' => ':attribute må ikke have mere end :max elementer.',
        'file' => ':attribute må ikke være større end :max kilobytes.',
        'numeric' => ':attribute må ikke være større end :max.',
        'string' => ':attribute må ikke være længere end :max tegn.',
    ],
    'max_digits' => ':attribute må ikke have mere end :max cifre.',
    'mimes' => ':attribute skal være en fil af typen: :values.',
    'mimetypes' => ':attribute skal være en fil af typen: :values.',
    'min' => [
        'array' => ':attribute skal indeholde mindst :min elementer.',
        'file' => ':attribute skal være mindst :min kilobytes.',
        'numeric' => ':attribute skal være mindst :min.',
        'string' => ':attribute skal være mindst :min tegn.',
    ],
    'min_digits' => ':attribute skal have mindst :min cifre.',
    'missing' => ':attribute feltet skal være tomt.',
    'missing_if' => ':attribute feltet skal være tomt, når :other er :value.',
    'missing_unless' => ':attribute feltet skal være tomt, hvis :other er :value.',
    'missing_with' => ':Attribute-feltet skal være tomt, når :values er til stede.',
    'missing_with_all' => ':attribute feltet skal være tomt, når :values er til stede.',
    'multiple_of' => ':attribute skal være et multiplum af :value.',
    'not_in' => 'Den valgte :attribute er ugyldig.',
    'not_regex' => ':attribute formatet er ugyldigt.',
    'numeric' => ':attribute skal være et tal.',
    'password' => [
        'letters' => ':attribute skal indeholde mindst ét bogstav.',
        'mixed' => ':attribute skal indeholde mindst ét stort og et lille bogstav.',
        'numbers' => ':attribute skal indeholde mindst ét tal.',
        'symbols' => ':attribute skal indeholde mindst ét symbol.',
        'uncompromised' => 'Den givne :attribute er blevet lækket. Vælg venligst en anden :attribute.',
    ],
    'present' => ':attribute feltet må ikke være tomt.',
    'present_if' => ':attribute må ikke være tomt, når :other er :value.',
    'present_unless' => ':attribute må ikke være tomt, medmindre :other er :value.',
    'present_with' => ':attribute må ikke være tomt, når :values er til stede.',
    'present_with_all' => ':attribute må ikke være tomt, når :values er til stede.',
    'prohibited' => ':attribute er forbudt.',
    'prohibited_if' => ':attribute er forbudt, når :other er :value.',
    'prohibited_unless' => ':attribute er forbudt, medmindre :other er i :values.',
    'prohibits' => ':attribute forhindrer :other i at være til stede.',
    'regex' => ':attribute formatet er ugyldigt.',
    'required' => ':attribute skal udfyldes.',
    'required_array_keys' => ':attribute skal indeholde indstillinger for: :values.',
    'required_if' => ':attribute er krævet når :other er :value.',
    'required_if_accepted' => ':attribute er nødvendigt, når :other accepteres.',
    'required_unless' => ':attribute er påkrævet, medmindre :other er i :values.',
    'required_with' => ':attribute er påkrævet når :values is present.',
    'required_with_all' => ':attribute er påkrævet når :values are udfyldt.',
    'required_without' => ':attribute skal udfyldes når :values ikke er udfyldt.',
    'required_without_all' => ':attribute skal udfyldes når ingen af :values er udfyldt.',
    'same' => ':attribute og :other skal matche.',
    'size' => [
        'array' => ':attribute skal indeholde :size elementer.',
        'file' => ':attribute skal være :size kilobytes.',
        'numeric' => ':attribute skal være :size.',
        'string' => ':attribute skal være :size tegn.',
    ],
    'starts_with' => ':attribute skal starte med en af følgende: :values',
    'string' => ':attribute skal være en streng.',
    'timezone' => ':attribute skal være en gyldig tidszone.',
    'unique' => ':attribute er allerede blevet taget.',
    'uploaded' => ':attribute fejlede ved upload.',
    'uppercase' => ':attribute skal være i store bogstaver.',
    'url' => ':attribute er ikke en gyldig URL.',
    'ulid' => ':attribute skal være en gyldig ULID.',
    'uuid' => ':attribute skal være en gyldig UUID.',

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
            'rule-name' => 'brugerdefineret besked',
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
