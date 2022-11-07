<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Het following language lines contain Het default error messages used by
    | Het validator class. Some of Hetse rules have multiple versions such
    | as Het size rules. Feel free to tweak each of Hetse messages here.
    |
    */

    'accepted' => 'Het :attribute moet worden geaccepteerd.',
    'accepted_if' => 'Het :attribute moet worden geaccepteerd als :other :value is.',
    'active_url' => 'Het :attribute is geen geldige URL.',
    'after' => 'Het :attribute moet een datum na :date zijn.',
    'after_or_equal' => 'Het :attribute moet een datum na of gelijk aan :date zijn.',
    'alpha' => 'Het :attribute moet letters bevatten',
    'alpha_dash' => 'Het :attribute moet only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'Het :attribute moet only contain letters and numbers.',
    'array' => 'Het :attribute moet een array zijn.',
    'before' => 'Het :attribute moet een datum voor :date zijn.',
    'before_or_equal' => 'Het :attribute moet voor of gelijk aan :date zijn.',
    'between' => [
        'array' => 'Het :attribute moet tussen de :min en  :max  bevatten.',
        'file' => 'Het :attribute moet tussen de :min en :max kilobytes. zijn',
        'numeric' => 'Het :attribute moet tussen de :min en :max zijn.',
        'string' => 'Het :attribute moet tussen de:min en :max karrakters.',
    ],
    'boolean' => 'Het :attribute veld moet true of false zijn.',
    'confirmed' => 'Het :attribute bevestiging komt niet overeen.',
    'current_password' => 'Het wachtwoord is incorrect.',
    'date' => 'Het :attribute is geen geldige datum.',
    'date_equals' => 'Het :attribute moet een datum gelijk aan :date zijn.',
    'date_format' => 'Het :attribute does komt niet overeen met het formaat :format.',
    'declined' => 'Het :attribute moet geweigerd zijn.',
    'declined_if' => 'Het :attribute moet geweigerd zijn wanneer :other is :value.',
    'different' => 'Het :attribute en :other moet verschillen.',
    'digits' => 'Het :attribute moet :digits cijfers zijn.',
    'digits_between' => 'Het :attribute moet tussen de :min en :max cijfers zijn.',
    'dimensions' => 'Het :attribute heeft ongeldige afbeeldingsafmetingen.',
    'distinct' => 'Het :attribute veld heeft een dubbele waarde.',
    'doesnt_end_with' => 'Het :attribute mag niet eindigen met een van de volgende: :values.',
    'doesnt_start_with' => 'Het :attribute mag niet beginnen met een van de volgende: :values.',
    'email' => 'Het :attribute moet een geldig e-mailadres zijn.',
    'ends_with' => 'Het :attribute moet eindigen met een van Het volgende: :values.',
    'enum' => 'Het geselecteerde :attribute is ongeldig.',
    'exists' => 'Het geselecteerde :attribute is ongeldig.',
    'file' => 'Het :attribute moet een bestand zijn.',
    'filled' => 'Het :attribute veld moet een waarde hebben.',
    'gt' => [
        'array' => 'Het :attribute moet meer hebben dan :value items.',
        'file' => 'Het :attribute moet groter zijn dan :value kilobytes.',
        'numeric' => 'Het :attribute moet groter zijn dan :value.',
        'string' => 'Het :attribute moet groter zijn dan :value karakters.',
    ],
    'gte' => [
        'array' => 'Het :attribute moet een :value items of meer.',
        'file' => 'Het :attribute moet groter zijn dan of gelijk aan :value kilobytes.',
        'numeric' => 'Het :attribute moet groter zijn dan of gelijk aan :value.',
        'string' => 'Het :attribute moet groter zijn dan of gelijk aan :value karakters.',
    ],
    'image' => 'Het :attribute moet een afbeelding zijn.',
    'in' => 'Het geselecteerde :attribute is ongeldig.',
    'in_array' => 'Het :attribute veld bestaat niet in :other.',
    'integer' => 'Het :attribute moet een geheel getal zijnr.',
    'ip' => 'Het :attribute moet een geldig IP Adres zijn',
    'ipv4' => 'Het :attribute moet een geldig IPv4-adres zijn.',
    'ipv6' => 'Het :attribute moet een geldig IPv6-adres zijn.',
    'json' => 'Het :attribute moet een geldige JSON-string zijn.',
    'lt' => [
        'array' => 'Het :attribute moet have less than :value items.',
        'file' => 'Het :attribute moet be less than :value kilobytes.',
        'numeric' => 'Het :attribute moet be less than :value.',
        'string' => 'Het :attribute moet be less than :value characters.',
    ],
    'lte' => [
        'array' => 'Het :attribute moet not have more than :value items.',
        'file' => 'Het :attribute moet be less than or equal to :value kilobytes.',
        'numeric' => 'Het :attribute moet be less than or equal to :value.',
        'string' => 'Het :attribute moet be less than or equal to :value characters.',
    ],
    'mac_address' => 'Het :attribute moet be a valid MAC address.',
    'max' => [
        'array' => 'Het :attribute moet not have more than :max items.',
        'file' => 'Het :attribute moet not be greater than :max kilobytes.',
        'numeric' => 'Het :attribute moet not be greater than :max.',
        'string' => 'Het :attribute moet not be greater than :max characters.',
    ],
    'max_digits' => 'Het :attribute moet not have more than :max digits.',
    'mimes' => 'Het :attribute moet be a file of type: :values.',
    'mimetypes' => 'Het :attribute moet be a file of type: :values.',
    'min' => [
        'array' => 'Het :attribute moet have at least :min items.',
        'file' => 'Het :attribute moet be at least :min kilobytes.',
        'numeric' => 'Het :attribute moet be at least :min.',
        'string' => 'Het :attribute moet be at least :min characters.',
    ],
    'min_digits' => 'Het :attribute moet have at least :min digits.',
    'multiple_of' => 'Het :attribute moet be a multiple of :value.',
    'not_in' => 'Het selected :attribute is invalid.',
    'not_regex' => 'Het :attribute format is invalid.',
    'numeric' => 'Het :attribute moet be a number.',
    'password' => [
        'letters' => 'Het :attribute moet contain at least one letter.',
        'mixed' => 'Het :attribute moet contain at least one uppercase and one lowercase letter.',
        'numbers' => 'Het :attribute moet contain at least one number.',
        'symbols' => 'Het :attribute moet contain at least one symbol.',
        'uncompromised' => 'Het given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present' => 'Het :attribute field moet be present.',
    'prohibited' => 'Het :attribute field is prohibited.',
    'prohibited_if' => 'Het :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'Het :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'Het :attribute field prohibits :other from being present.',
    'regex' => 'Het :attribute format is invalid.',
    'required' => 'Het :attribute field is required.',
    'required_array_keys' => 'Het :attribute field moet contain entries for: :values.',
    'required_if' => 'Het :attribute field is required when :other is :value.',
    'required_if_accepted' => 'Het :attribute field is required when :other is accepted.',
    'required_unless' => 'Het :attribute field is required unless :other is in :values.',
    'required_with' => 'Het :attribute field is required when :values is present.',
    'required_with_all' => 'Het :attribute field is required when :values are present.',
    'required_without' => 'Het :attribute field is required when :values is not present.',
    'required_without_all' => 'Het :attribute field is required when none of :values are present.',
    'same' => 'Het :attribute and :other moet match.',
    'size' => [
        'array' => 'Het :attribute moet contain :size items.',
        'file' => 'Het :attribute moet be :size kilobytes.',
        'numeric' => 'Het :attribute moet be :size.',
        'string' => 'Het :attribute moet be :size characters.',
    ],
    'starts_with' => 'Het :attribute moet start with one of Het following: :values.',
    'string' => 'Het :attribute moet be a string.',
    'timezone' => 'Het :attribute moet be a valid timezone.',
    'unique' => 'Het :attribute has already been taken.',
    'uploaded' => 'Het :attribute failed to upload.',
    'url' => 'Het :attribute moet be a valid URL.',
    'uuid' => 'Het :attribute moet be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using Het
    | convention "attribute.rule" to name Het lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'g-recaptcha-response' => [
            'required' => 'Het recaptcha field is required.',
            'recaptcha' => 'Het recaptcha response field is invalid.',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Het following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
