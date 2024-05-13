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

    'accepted' => ':attribute måste accepteras.',
    'active_url' => ':attribute är inte en giltig URL.',
    'after' => ':attribute måste vara ett datum efter :date.',
    'after_or_equal' => ':attribute måste vara ett datum efter eller under :date.',
    'alpha' => ':attribute kan enbart bestå av bokstäver.',
    'alpha_dash' => ':attribute kan enbart bestå av bokstäver, siffror, bindestreck och understreck.',
    'alpha_num' => ':attribute kan enbart bestå av bokstäver och siffror.',
    'array' => ':attribute måste vara en array.',
    'before' => ':attribute måste vara ett datum innan :date.',
    'before_or_equal' => ':attribute måste vara ett datum efter eller under :date.',
    'between' => [
        'numeric' => ':attribute måste vara mellan :min och :max.',
        'file' => ':attribute måste vara mellan :min och :max kilobyte.',
        'string' => ':attribute måste vara mellan :min och :max tecken.',
        'array' => ':attribute måste vara mellan :min och :max artiklar.',
    ],
    'boolean' => ':attribute måste vara sant eller falskt.',
    // ...
    'confirmed' => 'din bekräftelse av :attribute stämde inte överens.',
    'date' => ':attribute är inte ett giltigt datum.',
    'date_equals' => ':attribute måste vara följande datum: :date.',
    'date_format' => ':attribute stämmer inte överens med formatet :format.',
    'different' => ':attribute och :other måste vara olika.',
    'digits' => ':attribute måste vara :digits siffror.',
    'digits_between' => ':attribute måste vara mellan :min och :max siffror.',
    'dimensions' => ':attribute har ogiltiga bilddimensioner/bildmått.',
    'distinct' => ':attribute har ett duplicerat värde.',
    'email' => ':attribute måste vara en giltig e-postadress.',
    'ends_with' => ':attribute måste sluta med en av följande: :values.',
    'exists' => 'Det valda värdet/attributet :attribute är ogiltigt.',
    'file' => ':attribute måste vara en fil.',
    'filled' => ':attribute måste ha ett värde.',
    'gt' => [
        'numeric' => ':attribute måste vara större än :value.',
        'file' => ':attribute måste vara större än :value kilobytes.',
        'string' => ':attribute måste vara större än :value tecken.',
        'array' => ':attribute måste vara större än :value artiklar.',
    ],
    'gte' => [
        'numeric' => ':attribute måste vara större än eller lika med :value.',
        'file' => ':attribute måste vara större än eller lika med :value kilobyte.',
        'string' => ':attribute måste vara större än eller lika med :value tecken.',
        'array' => ':attribute måste :value artiklar eller fler.',
    ],
    'image' => ':attribute måste vara en bild.',
    'in' => 'Den/det valda :attribute är ogiltigt.',
    'in_array' => ':attribute fältet finns inte i :other.',
    'integer' => ':attribute måste vara en integer.',
    'ip' => ':attribute måste vara en giltig IP-adress.',
    'ipv4' => ':attribute måste vara en giltig IPv4-adress.',
    'ipv6' => ':attribute måste vara en giltig IPv6-adress.',
    'json' => ':attribute måste vara en giltig JSON-sträng.',
    'lt' => [
        'numeric' => ':attribute måste vara mindre än :value.',
        'file' => ':attribute måste vara mindre än :value kilobyte.',
        'string' => ':attribute måste vara mindre än :value tecken.',
        'array' => ':attribute måste vara mindre än :value artiklar.',
    ],
    'lte' => [
        'numeric' => ':attribute måste vara mindre än eller lika med :value.',
        'file' => ':attribute måste vara mindre än eller lika med :value kilobyte.',
        'string' => ':attribute måste vara mindre än eller lika med :value tecken.',
        'array' => ':attribute måste vara mindre än eller lika med :value artiklar.',
    ],
    'max' => [
        'numeric' => ':attribute kan inte var större än :max.',
        'file' => ':attribute kan inte var större än :max kilobyte.',
        'string' => ':attribute kan inte var större än :max tecken.',
        'array' => ':attribute kan inte ha mer än :max artiklar.',
    ],
    'mimes' => ':attribute måste vara en fil av typen: :values.',
    'mimetypes' => ':attribute måste vara en fil av typen: :values.',
    'min' => [
        'numeric' => ':attribute måste vara åtminstone :min.',
        'file' => ':attribute måste vara åtminstone :min kilobyte.',
        'string' => ':attribute måste vara åtminstone :min tecken.',
        // Again with the items...
        'array' => ':attribute måste ha åtminstone :min artiklar.',
    ],
    'multiple_of' => ':attribute måste vara en multipel av :value.',
    'not_in' => 'valt :attribute är ogiltigt.',
    'not_regex' => ':attribute formatet är ogiltigt.',
    'numeric' => ':attribute måste vara ett nummer.',
    'password' => 'lösenordet är inkorrekt.',
    'present' => ':attribute fältet måste vara närvarande.',
    'regex' => ':attribute formatet är ogiltigt.',
    'required' => ':attribute fältet är obligatoriskt.',
    'required_if' => ':attribute fältet är obligatoriskt när :other är :value.',
    'required_unless' => ':attribute fältet är obligatoriskt såvida :other inte är :values.',
    'required_with' => ':attribute fältet är obligatoriskt när :values är närvarande.',
    'required_with_all' => ':attribute fältet är obligatoriskt när :values är närvarande.',
    'required_without' => ':attribute fältet är obligatoriskt när :values inte är närvarande.',
    'required_without_all' => ':attribute fältet är obligatoriskt när inget av :values är närvarande.',
    'same' => ':attribute och :other måste matcha.',
    'size' => [
        'numeric' => ':attribute måste vara :size.',
        'file' => ':attribute måste vara :size kilobyte.',
        'string' => ':attribute måste vara :size tecken.',
        'array' => ':attribute måste innehålla :size artiklar.',
    ],
    'starts_with' => ':attribute måste börja med en av följande värden: :values.',
    'string' => ':attribute måste vara en sträng.',
    'timezone' => ':attribute måste vara en giltig tidszon.',
    'unique' => ':attribute är upptaget.',
    'uploaded' => 'misslyckade att ladda upp: :attribute.',
    'url' => ':attribute formatet är ogiltigt.',
    'uuid' => ':attribute måste vara ett giltigt UUID (universellt unikt id).',

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
