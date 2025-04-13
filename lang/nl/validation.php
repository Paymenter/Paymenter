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

    'accepted' => ':attribute moet worden geaccepteerd.',
    'accepted_if' => ':attribute moet worden geaccepteerd als :other :value is.',
    'active_url' => ':attribute moet een geldige URL zijn.',
    'after' => ':attribute moet een datum na :date zijn.',
    'after_or_equal' => ':attribute moet een datum na of gelijk aan :date zijn.',
    'alpha' => ':attribute mag alleen letters bevatten.',
    'alpha_dash' => ':attribute mag alleen letters, nummers, underscores (_) en streepjes (-) bevatten.',
    'alpha_num' => ':attribute mag alleen letters en nummers bevatten.',
    'array' => ':attribute moet een reeks zijn.',
    'ascii' => ':attribute mag alleen alfanumerieke tekens en symbolen van één byte bevatten.',
    'before' => ':attribute moet een datum vóór :date zijn.',
    'before_or_equal' => ':attribute moet een datum vóór of gelijk aan :date zijn.',
    'between' => [
        'array' => ':attribute moet tussen :min en :max items bevatten.',
        'file' => ':attribute moet tussen :min en :max kilobytes zijn.',
        'numeric' => ':attribute moet tussen :min en :max zijn.',
        'string' => ':attribute moet tussen :min en :max karakters zijn.',
    ],
    'boolean' => ':attribute moet ja of nee zijn.',
    'can' => ':attribute bevat een ongeldige waarde.',
    'confirmed' => 'Bevestiging van :attribute komt niet overeen.',
    'current_password' => 'Het wachtwoord is onjuist.',
    'date' => ':attribute dient een geldige datum te bevatten.',
    'date_equals' => ':attribute moet een datum gelijk aan :date zijn.',
    'date_format' => ':attribute dient overeen te komen met het format :format.',
    'decimal' => ':attribute moet :decimal decimalen hebben.',
    'declined' => ':attribute moet afgewezen worden.',
    'declined_if' => ':attribute moet afgewezen worden wanneer :other gelijk is aan :value.',
    'different' => ':attribute en :other mogen niet hetzelfde zijn.',
    'digits' => ':attribute moet bestaan uit :digits cijfers.',
    'digits_between' => ':attribute moet tussen :min en :max cijfers zijn.',
    'dimensions' => ':attribute heeft een ongeldig afbeeldingsformaat.',
    'distinct' => ':attribute heeft een dubbele waarde.',
    'doesnt_end_with' => ':attribute mag niet eindigen met één van de volgende waarden: :values.',
    'doesnt_start_with' => ':attribute mag niet beginnen met één van de volgende waarden: :values.',
    'email' => ':attribute dient een geldig e-mailadres te zijn.',
    'ends_with' => ':attribute moet met één van de volgende waarden eindigen: :values.',
    'enum' => 'Geselecteerde :attribute is ongeldig.',
    'exists' => 'Geselecteerde :attribute is ongeldig.',
    'extensions' => ':attribute moet een van de volgende extensies hebben: :values.',
    'file' => ':attribute moet een bestand zijn.',
    'filled' => ':attribute moet een waarde hebben.',
    'gt' => [
        'array' => ':attribute moet meer dan :value waardes bevatten.',
        'file' => ':attribute moet groter zijn dan :value kilobytes.',
        'numeric' => ':attribute moet groter zijn dan :value.',
        'string' => ':attribute moet meer dan :value tekens bevatten.',
    ],
    'gte' => [
        'array' => ':attribute moet :value of meer waardes bevatten.',
        'file' => ':attribute moet groter of gelijk zijn aan :value kilobytes.',
        'numeric' => ':attribute moet groter of gelijk zijn aan :value.',
        'string' => ':attribute moet groter of gelijk zijn aan :value tekens.',
    ],
    'hex_color' => ':attribute moet een geldige hexadecimale kleurcode zijn.',
    'image' => ':attribute moet een afbeelding zijn.',
    'in' => 'De geselecteerde :attribute is ongeldig.',
    'in_array' => ':attribute dient te bestaan in :other.',
    'integer' => ':attribute moet een getal zijn.',
    'ip' => ':attribute moet een geldig IP-adres zijn.',
    'ipv4' => ':ttribute moet een geldig IPv4-adres zijn.',
    'ipv6' => ':attribute moet een geldig IPv6-adres zijn.',
    'json' => ':attribute moet een geldige JSON-string zijn.',
    'list' => ':attribute moet een lijst zijn.',
    'lowercase' => ':attribute mag alleen kleine letters bevatten.',
    'lt' => [
        'array' => ':attribute moet minder dan :value waardes bevatten.',
        'file' => ':attribute moet kleiner zijn dan :value kilobytes.',
        'numeric' => ':attribute moet kleiner zijn dan :value.',
        'string' => ':attribute moet minder dan :value tekens bevatten.',
    ],
    'lte' => [
        'array' => ':attribute moet :value of minder waardes bevatten.',
        'file' => ':attribute moet kleiner of gelijk zijn aan :value kilobytes.',
        'numeric' => ':attribute moet kleiner of gelijk zijn aan :value.',
        'string' => ':attribute mag maximaal :value tekens bevatten.',
    ],
    'mac_address' => ':attribute moet een geldig MAC-adres zijn.',
    'max' => [
        'array' => ':attribute mag niet meer dan :max waardes bevatten.',
        'file' => ':attribute mag niet meer dan :max kilobytes zijn.',
        'numeric' => ':attribute mag niet hoger dan :max zijn.',
        'string' => ':attribute mag niet uit meer dan :max tekens bestaan.',
    ],
    'max_digits' => ':attribute mag niet meer dan :max cijfers bevatten.',
    'mimes' => ':attribute moet een bestand zijn van het type: :values.',
    'mimetypes' => ':attribute moet een bestand zijn van het type: :values.',
    'min' => [
        'array' => ':attribute moet minimaal :min waardes bevatten.',
        'file' => ':attribute moet minimaal :min kilobytes zijn.',
        'numeric' => ':attribute moet minimaal :min zijn.',
        'string' => ':attribute moet minimaal :min tekens zijn.',
    ],
    'min_digits' => ':attribute moet minimaal uit :min cijfers bestaan.',
    'missing' => ':attribute moet leeg zijn.',
    'missing_if' => ':attribute moet leeg zijn als :other :value is.',
    'missing_unless' => ':attribute moet leeg zijn tenzij :other :value is.',
    'missing_with' => ':attribute moet leeg zijn wanneer :values aanwezig is.',
    'missing_with_all' => ':attribute moet leeg zijn als :values aanwezig zijn.',
    'multiple_of' => ':attribute moet een veelvoud van :value zijn.',
    'not_in' => 'De geselecteerde :attribute is ongeldig.',
    'not_regex' => 'Het formaat van :attribute is ongeldig.',
    'numeric' => ':attribute moet een getal zijn.',
    'password' => [
        'letters' => ':attribute moet minimaal één letter bevatten.',
        'mixed' => ':attribute moet minimaal één kleine letter en één hoofdletter bevatten.',
        'numbers' => ':attribute moet minimaal één cijfer bevatten.',
        'symbols' => ':attribute moet minimaal één symbool bevatten.',
        'uncompromised' => 'De opgegeven :attribute komt voor in een datalek. Kies een ander :attribute.',
    ],
    'present' => ':attribute mag niet leeg zijn.',
    'present_if' => ':attribute mag niet leeg zijn als :other :value is.',
    'present_unless' => ':attribute mag niet leeg zijn tenzij :other :value is.',
    'present_with' => ':attribute mag niet leeg zijn als :values aanwezig is.',
    'present_with_all' => ':attribute mag niet leeg zijn als :values aanwezig zijn.',
    'prohibited' => ':attribute is verboden.',
    'prohibited_if' => ':attribute is verboden wanneer :other :value is.',
    'prohibited_unless' => ':attribute is verboden tenzij :other in :values staat.',
    'prohibits' => ':attribute is niet toegestaan in combinatie met :other.',
    'regex' => 'Het formaat van :attribute is ongeldig.',
    'required' => ':attribute is verplicht.',
    'required_array_keys' => ':attribute moet waardes bevatten voor :values.',
    'required_if' => ':attribute is verplicht indien :other gelijk is aan :value.',
    'required_if_accepted' => ':attribute is verplicht indien :other is geaccepteerd.',
    'required_unless' => ':attribute is verplicht tenzij :other gelijk is aan :values.',
    'required_with' => ':attribute mag niet leeg zijn als :values aanwezig is.',
    'required_with_all' => ':attribute mag niet leeg zijn als :values aanwezig zijn.',
    'required_without' => ':attribute mag niet leeg zijn als :values niet ingevuld is.',
    'required_without_all' => ':attribute mag niet leeg zijn wanneer geen van :values aanwezig zijn.',
    'same' => ':attribute en :other moeten overeenkomen.',
    'size' => [
        'array' => ':attribute moet :size waardes bevatten.',
        'file' => ':attribute moet :size kilobytes groot zijn.',
        'numeric' => ':attribute moet :size zijn.',
        'string' => ':attribute moet :size tekens zijn.',
    ],
    'starts_with' => ':attribute moet beginnen met een van de volgende: :values.',
    'string' => ':attribute moet een tekst zijn.',
    'timezone' => ':attribute moet een geldige tijdzone zijn.',
    'unique' => ':attribute is al in gebruik.',
    'uploaded' => 'Het uploaden van :attribute is mislukt.',
    'uppercase' => ':Attribute mag alleen hoofdletters bevatten.',
    'url' => ':attribute moet een geldige URL zijn.',
    'ulid' => ':attribute moet een geldige ULID zijn.',
    'uuid' => ':attribute moet een geldige UUID zijn.',

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
