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

    'accepted' => ':attribute feltet må aksepteres.',
    'accepted_if' => 'Feltet :attribute må aksepteres når :other er :value.',
    'active_url' => ':attribute feltet må være en gyldig URL.',
    'after' => ':attribute feltet må være en dato etter :date.',
    'after_or_equal' => ':attribute må være en dato etter eller lik :date.',
    'alpha' => ':attribute må kun inneholde bokstaver.',
    'alpha_dash' => ':attribute kan kun inneholde bokstaver, tall, bindestreker og understreker.',
    'alpha_num' => ':attribute feltet må kun inneholde bokstaver og tall.',
    'array' => 'Attributtfeltet må være en liste.',
    'ascii' => 'Attributtfeltet kan bare inneholde alfabetiske tegn og symboler.',
    'before' => ':attribute feltet må være en dato før :date.',
    'before_or_equal' => ':attribute  feltet må være en dato før eller lik  :date.',
    'between' => [
        'array' => ':attribute feltet må ha mellom :min og :max elementer.',
        'file' => ':attribute feltet må være mellom :min og :max kilobytes.',
        'numeric' => ':attribute feltet må være mellom :min og :max.',
        'string' => ':attribute feltet må være mellom :min og :max tegn.',
    ],
    'boolean' => ':attribute må være sann eller usann.',
    'can' => ':attribute feltet inneholder en uautorisert verdi.',
    'confirmed' => ':attribute bekreftelsen er ikke like.',
    'current_password' => 'Passordet er feil.',
    'date' => ':attribute må være en gyldig dato.',
    'date_equals' => ':attribute feltet må være en dato lik :date.',
    'date_format' => ':attribute må samsvare med formatet :format.',
    'decimal' => ':attribute må ha :decimal tegn.',
    'declined' => ':attribute må reduseres.',
    'declined_if' => ':attribute må avvises når :other er :value',
    'different' => ':attribute og :other er forskjellig.',
    'digits' => ':attribute må være :digits sifre.',
    'digits_between' => ':attribute må være mellom :min og :max sifre.',
    'dimensions' => ':attribute har ugyldige bildedimensjoner.',
    'distinct' => ':attribute har en duplikat verdi.',
    'doesnt_end_with' => ':attribute må ikke slutte med en av følgende: :values',
    'doesnt_start_with' => ':attribute må ikke starte med en av følgende: :values.',
    'email' => ':attribute må være en gyldig e-postadresse.',
    'ends_with' => ':attribute må ende med én av følgende: :values.',
    'enum' => 'Den valgte :attribute er ugyldig.',
    'exists' => 'Den valgte :attribute er ugyldig.',
    'extensions' => ':attribute må ha en av følgende utvidelser: :values.',
    'file' => ':attribute må være en fil.',
    'filled' => ':attribute må ha en verdi.',
    'gt' => [
        'array' => ':attribute må inneholde mer enn :value elementer.',
        'file' => ':attribute må være større enn :value kilobyte.',
        'numeric' => ':attribute må være større enn :value.',
        'string' => ':attribute må være større enn :value tegn.',
    ],
    'gte' => [
        'array' => ':attribute må ha :value elementer eller mer.',
        'file' => ':attribute må være større enn eller lik :value kilobyte.',
        'numeric' => ':attribute må være større enn eller lik :value.',
        'string' => ':attribute må være større eller lik :value tegn.',
    ],
    'hex_color' => ':attribute må være en gyldig heksadesimalfarge.',
    'image' => ':attribute må være et bilde.',
    'in' => 'Den valgte :attribute er ugyldig.',
    'in_array' => ':attribute må finnes i :other.',
    'integer' => ':attribute må være et heltall.',
    'ip' => ':attribute må være en gyldig IP adresse.',
    'ipv4' => ':attribute må være en gyldig IPv4 adresse.',
    'ipv6' => ':attribute må være en gyldig IPv6 adresse.',
    'json' => ':attribute må være en gyldig JSON streng.',
    'list' => ':attribute må være en liste.',
    'lowercase' => ':attribute feltet må være små bokstaver.',
    'lt' => [
        'array' => ':attribute må ha mindre enn :value elementer.',
        'file' => ':attribute må være mindre enn :value kilobytes.',
        'numeric' => ':attribute må være mindre enn :value.',
        'string' => ':attribute må være mindre enn :value tegn.',
    ],
    'lte' => [
        'array' => ':attribute må ikke inneholde mer enn :value elementer.',
        'file' => ':attribute må være mindre enn eller lik :value kilobytes.',
        'numeric' => ':attribute må være mindre enn eller lik :value.',
        'string' => ':attribute må være mindre enn eller lik :value tegn.',
    ],
    'mac_address' => ':attribute må være en gyldig MAC adresse.',
    'max' => [
        'array' => ':attribute må ikke inneholde mer enn :max elementer.',
        'file' => ':attribute må ikke være større enn :max kilobytes.',
        'numeric' => ':attribute må ikke være større enn :max.',
        'string' => ':attribute kan ikke være større enn :max tegn.',
    ],
    'max_digits' => ':attribute må ikke inneholde mer enn :max sifre.',
    'mimes' => ':attribute må være en fil av typen: :values.',
    'mimetypes' => ':attribute må være en fil av typen: :values.',
    'min' => [
        'array' => ':attribute må ha minst :min elementer.',
        'file' => ':attribute må være minst :min kilobytes.',
        'numeric' => ':attribute må være minst :min.',
        'string' => ':attribute må være minst :min tegn.',
    ],
    'min_digits' => ':attribute må ha minst :min sifre.',
    'missing' => ':attribute feltet mangler.',
    'missing_if' => ':attribute mangler når :other er :value.',
    'missing_unless' => ':attribute mangler når :other er :value.',
    'missing_with' => ':attribute mangler når :values er tilstede.',
    'missing_with_all' => ':attribute mangler når :values er tilstede.',
    'multiple_of' => ':attribute må være et flere av :value.',
    'not_in' => 'Valgt :attribute er ugyldig.',
    'not_regex' => ':attribute er ugyldig.',
    'numeric' => ':attribute må være et nummer.',
    'password' => [
        'letters' => ':attribute må inneholde minst én bokstav.',
        'mixed' => ':attribute må inneholde minst en stor bokstav og en liten bokstav.',
        'numbers' => ':attribute må inneholde minst ett nummer.',
        'symbols' => ':attribute må inneholde minst ett symbol.',
        'uncompromised' => ':attribute har dukket opp i en datalekkasje. Velg et annet :attributt.',
    ],
    'present' => ':attribute må være tilstede.',
    'present_if' => ':attribute må finnes når :other er :value',
    'present_unless' => ':attribute må finnes med mindre :other er :value.',
    'present_with' => ':attribute må finnes når :values er tilstede.',
    'present_with_all' => ':attribute må finnes når :values er tilstede.',
    'prohibited' => ':attribute er forbudt.',
    'prohibited_if' => ':attribute er forbudt når :other er :value.',
    'prohibited_unless' => ':attribute er forbudt med mindre :other er i :values.',
    'prohibits' => ':attribute forbyr :other fra å være tilstede.',
    'regex' => ':attribute er ugyldig.',
    'required' => ':attribute er påkrevd.',
    'required_array_keys' => ':attribute må inneholde enheter for: :values.',
    'required_if' => ':attribute er påkrevd når :other er :value.',
    'required_if_accepted' => ':attribute feltet kreves når :other er godkjent.',
    'required_unless' => ' :attribute er påkrevd med mindre :other er i :values.',
    'required_with' => ':attribute er påkrevd når :values er tilstede.',
    'required_with_all' => ':attribute er påkrevd når :values er tilstede.',
    'required_without' => ':attribute er påkrevd når :values ikke er tilstede.',
    'required_without_all' => ':attribute er påkrevd når ingen av :values er tilstede.',
    'same' => ':attribute må samsvare med :other.',
    'size' => [
        'array' => ':attribute må inneholde :size elementer.',
        'file' => ':attribute må være :size kilobytes.',
        'numeric' => ':attribute må være :size.',
        'string' => ':attribute må være :size tegn.',
    ],
    'starts_with' => ':attribute må starte med en av følgende: :values.',
    'string' => ':attribute må være en tekst.',
    'timezone' => ':attribute må være en gyldig tidssone.',
    'unique' => ':attribute er allerede brukt.',
    'uploaded' => ':attribute opplasting feilet.',
    'uppercase' => ':attribute må være store bokstaver.',
    'url' => ':attribute må være en gyldig URL.',
    'ulid' => ':attribute må være en gyldig ULID.',
    'uuid' => ':attribute må være en gyldig UUID.',

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
            'rule-name' => 'egendefinert-melding',
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
