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
    'active_url' => ':attribute er ikke en gyldig webadresse.',
    'after' => ':attribute skal være en dato efter :date.',
    'after_or_equal' => ':attribute skal være en dato efter eller svarende til :date.',
    'alpha' => ':attribute må kun indeholde bogstaver.',
    'alpha_dash' => ':attribute må kun indeholde bogstaver, tal, bindestreger og understregninger.',
    'alpha_num' => ':attribute må kun indeholde bogstaver og tal.',
    'array' => ':attribute skal være en array.',
    'before' => ':attribute skal være en dato før :date.',
    'before_or_equal' => ':attribute skal være en dato før eller svarende til :date.',
    'between' => [
        'numeric' => ':attribute skal være mellem :min og :max.',
        'file' => ':attribute skal være mellem :min and :max kilobytes.',
        'string' => ':attribute skal være mellem :min and :max characters.',
        'array' => ':attribute skal have mellem :min og :max elementer.',
    ],
    'boolean' => ':attribute feltet skal være sandt eller falsk.',
    'confirmed' => ':attribute bekræftelse stemmer ikke overens.',
    'date' => ':attribute er ikke en gyldig dato.',
    'date_equals' => ':attribute skal være en dato svarende til :date.',
    'date_format' => ':attribute svarer ikke til formatet :format.',
    'different' => ':attribute og :other skal være forskellige.',
    'digits' => ':attribute skal være :digits cifre.',
    'digits_between' => ':attribute skal være mellem :min og :max cifre.',
    'dimensions' => ':attribute har ugyldige billeddimensioner.',
    'distinct' => ':attribute har en dubletværdi.',
    'email' => ':attribute skal være en gyldig e-mailadresse.',
    'ends_with' => ':attribute skal slutte med et af følgende: :values.',
    'exists' => 'Den valgte :attribute er ugyldig.',
    'file' => ':attribute skal være en fil.',
    'filled' => ':attribute feltet skal have en værdi.',
    'gt' => [
        'numeric' => ':attribute skal være større end :value.',
        'file' => ':attribute skal være større end :value kilobytes.',
        'string' => ':attribute skal være længere end :value tegn.',
        'array' => ':attribute skal have flere end :value elementer.',
    ],
    'gte' => [
        'numeric' => ':attribute skal være større end eller lig med :value.',
        'file' => ':attribute skal være større end eller lig med :value kilobytes.',
        'string' => ':attribute skal være længere end eller lig med :value tegn.',
        'array' => ':attribute skal have :value elementer eller flere.',
    ],    
    'image' => ':attribute skal være et billede.',
    'in' => 'Den valgte :attribute er ugyldig.',
    'in_array' => ':attribute feltet eksisterer ikke i :other.',
    'integer' => ':attribute skal være et heltal.',
    'ip' => ':attribute skal være en gyldig IP-adresse.',
    'ipv4' => ':attribute skal være en gyldig IPv4-adresse.',
    'ipv6' => ':attribute skal være en gyldig IPv6-adresse.',
    'json' => ':attribute skal være en gyldig JSON-streng.',
    'lt' => [
        'numeric' => ':attribute skal være mindre end :value.',
        'file' => ':attribute skal være mindre end :value kilobytes.',
        'string' => ':attribute skal være mindre end :value tegn.',
        'array' => ':attribute skal have færre end :value elementer.',
    ],
    'lte' => [
        'numeric' => ':attribute skal være mindre end eller lig med :value.',
        'file' => ':attribute skal være mindre end eller lig med :value kilobytes.',
        'string' => ':attribute skal være mindre end eller lig med :value tegn.',
        'array' => ':attribute må ikke have flere end :value elementer.',
    ],
    'max' => [
        'numeric' => ':attribute må ikke være større end :max.',
        'file' => ':attribute må ikke være større end :max kilobytes.',
        'string' => ':attribute må ikke være længere end :max tegn.',
        'array' => ':attribute må ikke have flere end :max elementer.',
    ],
    'mimes' => ':attribute skal være en fil af typen: :values.',
    'mimetypes' => ':attribute skal være en fil af typen: :values.',
    'min' => [
        'numeric' => ':attribute skal være mindst :min.',
        'file' => ':attribute skal være mindst :min kilobytes.',
        'string' => ':attribute skal være mindst :min tegn.',
        'array' => ':attribute skal have mindst :min elementer.',
    ],
    'multiple_of' => ':attribute skal være et multiplum af :value.',
    'not_in' => 'Den valgte :attribute er ugyldig.',
    'not_regex' => ':attribute format er ugyldigt.',
    'numeric' => ':attribute skal være et tal.',
    'password' => 'Adgangskoden er forkert.',
    'present' => ':attribute feltet skal være til stede.',
    'regex' => ':attribute format er ugyldigt.',
    'required' => ':attribute feltet er påkrævet.',
    'required_if' => ':attribute feltet er påkrævet, når :other er :value.',
    'required_unless' => ':attribute feltet er påkrævet, medmindre :other er i :values.',
    'required_with' => ':attribute feltet er påkrævet, når :values er til stede.',
    'required_with_all' => ':attribute feltet er påkrævet, når :values er til stede.',
    'required_without' => ':attribute feltet er påkrævet, når :values ikke er til stede.',
    'required_without_all' => ':attribute feltet er påkrævet, når ingen af :values er til stede.',
    'same' => ':attribute og :other skal matche.',
    'size' => [
        'numeric' => ':attribute skal være :size.',
        'file' => ':attribute skal være :size kilobytes.',
        'string' => ':attribute skal være :size tegn.',
        'array' => ':attribute skal indeholde :size elementer.',
    ],
    'starts_with' => ':attribute skal starte med en af følgende: :values.',
    'string' => ':attribute skal være en streng.',
    'timezone' => ':attribute skal være en gyldig tidszone.',
    'unique' => ':attribute er allerede taget.',
    'uploaded' => ':attribute kunne ikke uploades.',
    'url' => ':attribute format er ugyldigt.',
    'uuid' => ':attribute skal være et gyldigt UUID.',

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
