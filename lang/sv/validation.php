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

    'accepted' => ':attribute fältet måste accepteras.',
    'accepted_if' => ':attribute fältet måste accepteras när :other är :value.',
    'active_url' => ':attribute fältet måste vara en giltig URL.',
    'after' => ':attribute fältet måste vara ett datum efter :date.',
    'after_or_equal' => ':attribute fältet-fältet måste vara ett datum efter eller samma som :date.',
    'alpha' => ':attribute fältet får endast innehålla bokstäver.',
    'alpha_dash' => ':attribute fältet får endast innehålla bokstäver, siffror, bindestreck eller understreck.',
    'alpha_num' => ':attribute fältet får endast innehålla bokstäver och siffror.',
    'array' => ':attribute fältet måste vara en lista.',
    'ascii' => ':attribute fältet får endast innehålla en single-byte alfanumeriska tecken och symboler.',
    'before' => ':attribute fältet måste vara ett datum före :date.',
    'before_or_equal' => ':attribute fältet måste vara ett datum före eller samma som :date.',
    'between' => [
        'array' => ':attribute fältet måste ha mellan :min och :max objekt.',
        'file' => ':attribute fältet måste vara mellan :min och :max kilobyte.',
        'numeric' => ':attribute fältet måste vara mellan :min och :max.',
        'string' => ':attribute fältet måste vara mellan :min och :max tecken.',
    ],
    'boolean' => ':attribute fältet måste vara sant eller falskt.',
    'can' => ':attribute fältet innehåller ett otillåtet värde.',
    'confirmed' => ':attribute fältets bekräftelse matchar inte.',
    'current_password' => 'Felaktigt lösenord.',
    'date' => ':attribute fältet måste ha ett giltigt datum.',
    'date_equals' => ':attribute fältets datum måste vara samma som :date.',
    'date_format' => ':attribute fältet måste matcha formatet :format.',
    'decimal' => ':attribute fältet måste ha :decimal decimaltecken.',
    'declined' => ':attribute fältet måste nekas.',
    'declined_if' => ':attribute fältet måste nekas när :other är :value.',
    'different' => ':attribute fältet och :other måste vara olika.',
    'digits' => ':attribute fältet måste vara :digits siffror.',
    'digits_between' => ':attribute fältet måste vara mellan :min och :max siffror.',
    'dimensions' => ':attribute fältet has inkorrekta bilddimensioner.',
    'distinct' => ':attribute fältet har ett duplicerat värde.',
    'doesnt_end_with' => ':attribute fältet får inte sluta med en av följande värden: :values.',
    'doesnt_start_with' => ':attribute får inte börja med något av följande: :values.',
    'email' => ':attribute fältet måste vara en giltig e-postadress.',
    'ends_with' => ':attribute fältet måste sluta med en av följande värden: :values.',
    'enum' => 'Det valda :attribute är ogiltigt.',
    'exists' => 'Det valda :attribute är ogiltigt.',
    'extensions' => 'Fältet :attribute måste ha något av följande tillägg: :values.',
    'file' => ':attribute field måste vara en fil.',
    'filled' => ':attribute fältet måste ha ett värde.',
    'gt' => [
        'array' => ':attribute fältet måste ha mer än :value objekt.',
        'file' => ':attribute fältet måste vara större än :value kilobyte.',
        'numeric' => ':attribute fältet måste vara större än :value.',
        'string' => ':attribute fältet måste vara större än :value tecken.',
    ],
    'gte' => [
        'array' => ':attribute måste ha :value objekt eller mer.',
        'file' => ':attribute fältet måste vara större än eller lika med :value kilobyte.',
        'numeric' => ':attribute fältet måste vara större än eller lika med :value.',
        'string' => ':attribute fältet måste vara större än eller lika med :value tecken.',
    ],
    'hex_color' => ':attribute fältet måste vara en giltig hexadecimalfärg.',
    'image' => ':attribute fältet måste vara en bild.',
    'in' => 'Det valda :attribute är ogiltigt.',
    'in_array' => ':attribute fältet måste existera i :other.',
    'integer' => ':attribute fältet måste vara ett heltal.',
    'ip' => ':attribute fältet måste vara en giltig IP-adress.',
    'ipv4' => ':attribute fältet måste vara en giltig IPv4-adress.',
    'ipv6' => ':attribute fältet måste vara en giltig IPv6-adress.',
    'json' => ':attribute fältet måste vara en giltig JSON-sträng.',
    'list' => ':attribute fältet måste vara en lista.',
    'lowercase' => ':attribute fältet måste vara liten bokstav.',
    'lt' => [
        'array' => ':attribute måste ha mindre än :value objekt.',
        'file' => ':attribute fältet måste vara mindre än :value kilobyte.',
        'numeric' => ':attribute måste vara mindre än :value.',
        'string' => ':attribute fältet måste vara färre än :value tecken.',
    ],
    'lte' => [
        'array' => ':attribute fältet får inte ha fler än :value objekt.',
        'file' => ':attribute fältet måste vara mindre än eller lika med :value kilobyte.',
        'numeric' => ':attribute fältet måste vara mindre än eller lika med :value.',
        'string' => ':attribute fältet måste vara färre än eller lika med :value tecken.',
    ],
    'mac_address' => ':attribute fältet måste vara en giltig MAC-adress.',
    'max' => [
        'array' => ':attribute fältet får inte ha fler än :max objekt.',
        'file' => ':attribute fältet får inte vara större än :max kilobyte.',
        'numeric' => ':attribute fältet får inte vara större än :max.',
        'string' => ':attribute fältet får inte vara större än :max tecken.',
    ],
    'max_digits' => ':attribute fältet får inte ha fler än :max siffror.',
    'mimes' => ':attribute fältet måste vara av filtyp: :values.',
    'mimetypes' => ':attribute fältet måste vara av filtyp: :values.',
    'min' => [
        'array' => ':attribute måste vara minst :min objekt.',
        'file' => ':attribute fältet måste vara minst :min kilobyte.',
        'numeric' => ':attribute fältet måste vara minst :min.',
        'string' => ':attribute fältet måste innehålla minst :min tecken.',
    ],
    'min_digits' => ':attribute fältet måste vara minst :min siffror.',
    'missing' => ':attribute måste saknas.',
    'missing_if' => 'Fältet :attribute måste saknas när :other är :value.',
    'missing_unless' => 'Fältet :attribute måste saknas när :other är :value.',
    'missing_with' => ':attribute fältet måste saknas när :values finns.',
    'missing_with_all' => ':attribute fältet måste saknas när :values finns.',
    'multiple_of' => ':attribute fältet måste vara multipel av :value.',
    'not_in' => 'Det valda :attribute är ogiltigt.',
    'not_regex' => ':attribute är ett ogiltigt format.',
    'numeric' => ':attribute fältet måste vara ett nummer.',
    'password' => [
        'letters' => ':attribute fältet måste innehålla minst en bokstav.',
        'mixed' => ':attribute måste innehålla minst en stor och en liten bokstav.',
        'numbers' => ':attribute fältet måste innehålla minst ett nummer.',
        'symbols' => ':attribute måste innehålla minst en symbol.',
        'uncompromised' => 'Det angivna :attribute har upptäckts i ett dataintrång. Vänligen välj ett annat :attribute.',
    ],
    'present' => ':attribute fältet måste vara ifyllt.',
    'present_if' => ':attribute fältet måste vara ifyllt om :other är :value.',
    'present_unless' => ':attribute fältet måste vara ifyllt så länge inte :other är :value.',
    'present_with' => 'Fältet :attribute måste finnas när :values är angivet.',
    'present_with_all' => ':attribute fältet måste vara ifyllt om :values är angivna.',
    'prohibited' => ':attribute fältet är förbjudet.',
    'prohibited_if' => ':attribute fältet är förbjudet när :other är :value.',
    'prohibited_unless' => ':attribute fältet är förbjudet om inte :other finns i :values.',
    'prohibits' => 'Fältet :attribute förhindrar :other från att visas.',
    'regex' => ':attribute är ett ogiltigt format.',
    'required' => ':attribute fältet obligatoriskt.',
    'required_array_keys' => ':attribute-fältet måste innehålla värden för: :values.',
    'required_if' => ':attribute fältet är obligatoriskt när :other är :value.',
    'required_if_accepted' => 'Fältet :attribute är obligatoriskt när :other är accepterat.',
    'required_unless' => ':attribute är obligatoriskt om inte :other finns i :values.',
    'required_with' => ':attribute fältet är obligatoriskt när :values är angivet.',
    'required_with_all' => 'Fältet :attribute är obligatoriskt när :values är angivet.',
    'required_without' => ':attribute fältet är obligatoriskt när :values inte är ifyllt.',
    'required_without_all' => ':attribute är obligatirskt när ingen av :values finns.',
    'same' => ':attribute fältet måste matcha :other.',
    'size' => [
        'array' => ':attribute fältet måste innehålla :size objekt.',
        'file' => ':attribute fältet måste vara :size kilobyte.',
        'numeric' => ':attribute-fältet måste vara :size.',
        'string' => ':attribute-fältet måste vara :size tecken.',
    ],
    'starts_with' => ':attribute fältet måste starta med minst ett av följande: :values.',
    'string' => ':attribute måste vara en sträng.',
    'timezone' => ':attribute-fältet måste vara en giltig tidszon.',
    'unique' => ':attribute har redan tagits.',
    'uploaded' => ':attribute kunde inte laddas upp.',
    'uppercase' => ':attribute fältet måste vara stor bokstav.',
    'url' => ':attribute fältet måste vara en giltig URL.',
    'ulid' => ':attribute fältet måste vara en korrekt ULID.',
    'uuid' => ':attribute fältet måste vara en korrekt UUID.',

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
            'rule-name' => 'anpassat meddelande',
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
