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

    'accepted' => 'Polje :attribute mora biti prihvaćeno.',
    'accepted_if' => 'Polje :attribute mora biti prihvaćeno kada je :other :value.',
    'active_url' => 'Polje :attribute mora biti validan URL.',
    'after' => 'Polje :attribute mora biti datum posle :date.',
    'after_or_equal' => 'Polje :attribute mora biti datum posle ili jednak :date.',
    'alpha' => 'Polje :attribute može sadržati samo slova.',
    'alpha_dash' => 'Polje :attribute može sadržati samo slova, brojeve, crtice i donje crte.',
    'alpha_num' => 'Polje :attribute može sadržati samo slova i brojeve.',
    'array' => 'Polje :attribute mora biti niz array.',
    'ascii' => 'Polje :attribute može sadržati samo jednobajtne alfanumeričke znakove i simbole.',
    'before' => 'Polje :attribute mora biti datum pre :date.',
    'before_or_equal' => 'Polje :attribute mora biti datum pre ili jednak :date.',
    'between' => [
        'array' => 'Polje :attribute mora imati između :min i :max stavki.',
        'file' => 'Polje :attribute mora biti između :min i :max kilobajta.',
        'numeric' => 'Polje :attribute mora biti između :min i :max.',
        'string' => 'Polje :attribute mora imati između :min i :max karaktera.',
    ],
    'boolean' => 'Polje :attribute mora biti tačno ili netačno.',
    'can' => 'Polje :attribute sadrži neovlašćenu vrednost.',
    'confirmed' => 'Potvrda polja :attribute se ne poklapa.',
    'current_password' => 'Lozinka je netačna.',
    'date' => 'Polje :attribute mora biti validan datum.',
    'date_equals' => 'Polje :attribute mora biti datum jednak :date.',
    'date_format' => 'Polje :attribute mora odgovarati formatu :format.',
    'decimal' => 'Polje :attribute mora imati :decimal decimalnih mesta.',
    'declined' => 'Polje :attribute mora biti odbijeno.',
    'declined_if' => 'Polje :attribute mora biti odbijeno kada je :other :value.',
    'different' => 'Polje :attribute i :other moraju biti različiti.',
    'digits' => 'Polje :attribute mora imati :digits cifara.',
    'digits_between' => 'Polje :attribute mora imati između :min i :max cifara.',
    'dimensions' => 'Polje :attribute ima nevalidne dimenzije slike.',
    'distinct' => 'Polje :attribute ima duplikat vrednost.',
    'doesnt_end_with' => 'Polje :attribute ne sme završavati sa: :values.',
    'doesnt_start_with' => 'Polje :attribute ne sme počinjati sa: :values.',
    'email' => 'Polje :attribute mora biti validna email adresa.',
    'ends_with' => 'Polje :attribute mora završavati sa jednim od sledećih: :values.',
    'enum' => 'Izabrano polje :attribute nije validno.',
    'exists' => 'Izabrano polje :attribute nije validno.',
    'extensions' => 'Polje :attribute mora imati jednu od sledećih ekstenzija: :values.',
    'file' => 'Polje :attribute mora biti fajl.',
    'filled' => 'Polje :attribute mora imati vrednost.',
    'gt' => [
        'array' => 'Polje :attribute mora imati više od :value stavki.',
        'file' => 'Polje :attribute mora biti veće od :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti veće od :value.',
        'string' => 'Polje :attribute mora imati više od :value karaktera.',
    ],
    'gte' => [
        'array' => 'Polje :attribute mora imati :value ili više stavki.',
        'file' => 'Polje :attribute mora biti veće ili jednako :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti veće ili jednako :value.',
        'string' => 'Polje :attribute mora imati najmanje :value karaktera.',
    ],
    'hex_color' => 'Polje :attribute mora biti validna heksadecimalna boja.',
    'image' => 'Polje :attribute mora biti slika.',
    'in' => 'Izabrano polje :attribute nije validno.',
    'in_array' => 'Polje :attribute mora postojati u :other.',
    'integer' => 'Polje :attribute mora biti ceo broj.',
    'ip' => 'Polje :attribute mora biti validna IP adresa.',
    'ipv4' => 'Polje :attribute mora biti validna IPv4 adresa.',
    'ipv6' => 'Polje :attribute mora biti validna IPv6 adresa.',
    'json' => 'The :attribute field must be a valid JSON string.',
    'list' => 'The :attribute field must be a list.',
    'lowercase' => 'The :attribute field must be lowercase.',
    'lt' => [
        'array' => 'The :attribute field must have less than :value items.',
        'file' => 'The :attribute field must be less than :value kilobytes.',
        'numeric' => 'The :attribute field must be less than :value.',
        'string' => 'The :attribute field must be less than :value characters.',
    ],
    'lte' => [
        'array' => 'The :attribute field must not have more than :value items.',
        'file' => 'The :attribute field must be less than or equal to :value kilobytes.',
        'numeric' => 'The :attribute field must be less than or equal to :value.',
        'string' => 'The :attribute field must be less than or equal to :value characters.',
    ],
    'mac_address' => 'The :attribute field must be a valid MAC address.',
    'max' => [
        'array' => 'The :attribute field must not have more than :max items.',
        'file' => 'The :attribute field must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute field must not be greater than :max.',
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'max_digits' => 'The :attribute field must not have more than :max digits.',
    'mimes' => 'The :attribute field must be a file of type: :values.',
    'mimetypes' => 'The :attribute field must be a file of type: :values.',
    'min' => [
        'array' => 'The :attribute field must have at least :min items.',
        'file' => 'The :attribute field must be at least :min kilobytes.',
        'numeric' => 'The :attribute field must be at least :min.',
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'min_digits' => 'The :attribute field must have at least :min digits.',
    'missing' => 'The :attribute field must be missing.',
    'missing_if' => 'The :attribute field must be missing when :other is :value.',
    'missing_unless' => 'The :attribute field must be missing unless :other is :value.',
    'missing_with' => 'The :attribute field must be missing when :values is present.',
    'missing_with_all' => 'The :attribute field must be missing when :values are present.',
    'multiple_of' => 'The :attribute field must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute field format is invalid.',
    'numeric' => 'Polje :attribute mora biti broj.',
    'password' => [
        'letters' => 'Polje :attribute mora sadržati najmanje jedno slovo.',
        'mixed' => 'Polje :attribute mora sadržati najmanje jedno veliko i jedno malo slovo.',
        'numbers' => 'Polje :attribute mora sadržati najmanje jedan broj.',
        'symbols' => 'Polje :attribute mora sadržati najmanje jedan simbol.',
        'uncompromised' => 'Uneto :attribute se pojavilo u curenju podataka. Molimo izaberite drugo :attribute.',
    ],
    'present' => 'Polje :attribute mora biti prisutno.',
    'present_if' => 'Polje :attribute mora biti prisutno kada je :other :value.',
    'present_unless' => 'Polje :attribute mora biti prisutno osim ako je :other :value.',
    'present_with' => 'Polje :attribute mora biti prisutno kada je :values prisutno.',
    'present_with_all' => 'Polje :attribute mora biti prisutno kada su :values prisutni.',
    'prohibited' => 'Polje :attribute je zabranjeno.',
    'prohibited_if' => 'Polje :attribute je zabranjeno kada je :other :value.',
    'prohibited_unless' => 'Polje :attribute je zabranjeno osim ako :other nije među vrednostima: :values.',
    'prohibits' => 'Polje :attribute onemogućava prisustvo polja :other.',
    'regex' => 'Format polja :attribute nije validan.',
    'required' => 'Polje :attribute je obavezno.',
    'required_array_keys' => 'Polje :attribute mora sadržati unose za: :values.',
    'required_if' => 'Polje :attribute je obavezno kada je :other :value.',
    'required_if_accepted' => 'Polje :attribute je obavezno kada je :other prihvaćeno.',
    'required_unless' => 'Polje :attribute je obavezno osim ako :other nije među sledećim vrednostima: :values.',
    'required_with' => 'Polje :attribute je obavezno kada je :values prisutno.',
    'required_with_all' => 'Polje :attribute je obavezno kada su :values prisutni.',
    'required_without' => 'Polje :attribute je obavezno kada :values nije prisutan.',
    'required_without_all' => 'Polje :attribute je obavezno kada nijedna od sledećih vrednosti nije prisutna: :values.',
    'same' => 'Polje :attribute mora se poklapati sa poljem :other.',
    'size' => [
        'array' => 'Polje :attribute mora sadržati :size stavki.',
        'file' => 'Polje :attribute mora imati :size kilobajta.',
        'numeric' => 'Polje :attribute mora biti :size.',
        'string' => 'Polje :attribute mora imati :size karaktera.',
    ],
    'starts_with' => 'Polje :attribute mora početi sa jednom od sledećih vrednosti: :values.',
    'string' => 'Polje :attribute mora biti tekstualni niz.',
    'timezone' => 'Polje :attribute mora biti validna vremenska zona.',
    'unique' => 'Vrednost polja :attribute je već zauzeta.',
    'uploaded' => 'Otpremanje polja :attribute nije uspelo.',
    'uppercase' => 'Polje :attribute mora biti napisano velikim slovima.',
    'url' => 'Polje :attribute mora biti validan URL.',
    'ulid' => 'Polje :attribute mora biti validan ULID.',
    'uuid' => 'Polje :attribute mora biti validan UUID.',

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
            'rule-name' => 'prilagođena poruka',
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
