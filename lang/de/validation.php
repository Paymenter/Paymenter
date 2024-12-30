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

    'accepted' => ':attribute muss akzeptiert werden.',
    'accepted_if' => 'Das :attribute muss akzeptiert werden, wenn :other :value ist.',
    'active_url' => ':Attribute muss eine URL sein.',
    'after' => ':attribute muss ein Datum nach dem :date sein.',
    'after_or_equal' => ':attribute muss ein Datum nach :date oder gleich :date sein.',
    'alpha' => ':attribute darf nur aus Buchstaben bestehen.',
    'alpha_dash' => ':Attribute darf nur aus Buchstaben, Zahlen, Binde- und Unterstrichen bestehen.',
    'alpha_num' => ':Attribute darf nur aus Buchstaben und Zahlen bestehen.',
    'array' => ':attribute muss ein Array sein.',
    'ascii' => ':attribute darf nur alphanumerische Zeichen und Symbole enthalten.',
    'before' => ':attribute muss ein Datum vor dem :date sein.',
    'before_or_equal' => ':attribute muss ein Datum vor dem :date oder gleich dem :date sein.',
    'between' => [
        'array' => ':attribute muss zwischen :min & :max Elemente haben.',
        'file' => ':attribute muss zwischen :min & :max Kilobytes groß sein.',
        'numeric' => ':Attribute muss zwischen :min & :max liegen.',
        'string' => ':attribute muss zwischen :min & :max Zeichen lang sein.',
    ],
    'boolean' => 'Das :attribute muss wahr oder falsch sein.',
    'can' => ':attribute enthält einen unzulässigen Wert.',
    'confirmed' => ':Attribute stimmt nicht mit der Bestätigung überein.',
    'current_password' => 'Das Passwort ist falsch.',
    'date' => ':attribute muss ein gültiges Datum sein.',
    'date_equals' => ':attribute muss ein Datum gleich :date sein.',
    'date_format' => ':attribute entspricht nicht dem gültigen Format für :format.',
    'decimal' => 'Die :attribute muss :decimal Dezimalstellen haben.',
    'declined' => 'Das :attribute muss abgelehnt werden.',
    'declined_if' => 'Das Feld „:attribute“ muss abgelehnt werden, wenn „:other“ den Wert „:value“ hat.',
    'different' => ':Attribute und :other müssen sich unterscheiden.',
    'digits' => ':attribute muss :digits Stellen haben.',
    'digits_between' => ':attribute muss zwischen :min und :max Stellen haben.',
    'dimensions' => 'Das Feld :attribute hat ungültige Bildabmessungen.',
    'distinct' => 'Der Wert von :attribute existiert bereits.',
    'doesnt_end_with' => ':Attribute darf nicht mit einem der folgenden Werte enden: :values.',
    'doesnt_start_with' => 'Das :attribute-Feld darf nicht mit einem der folgenden Werte beginnen: :values.',
    'email' => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'ends_with' => 'Das :attribute-Feld muss mit einem der folgenden Werte enden: :values.',
    'enum' => 'Das ausgewählte :attribute ist ungültig.',
    'exists' => 'Das ausgewählte :attribute ist ungültig.',
    'extensions' => 'Das Feld :attribute muss eine der folgenden Erweiterungen haben: :values.',
    'file' => 'Das :attribute Feld muss eine Datei sein.',
    'filled' => ':attribute muss ausgefüllt sein.',
    'gt' => [
        'array' => ':attribute muss mehr als :value Elemente haben.',
        'file' => ':attribute muss größer als :value Kilobytes sein.',
        'numeric' => ':attribute muss größer als :value sein.',
        'string' => ':attribute muss länger als :value Zeichen sein.',
    ],
    'gte' => [
        'array' => ':attribute muss mindestens :value Elemente haben.',
        'file' => ':attribute muss größer oder gleich :value Kilobytes sein.',
        'numeric' => ': attribute muss größer oder gleich :value sein.',
        'string' => ':attribute muss mindestens :value Zeichen lang sein.',
    ],
    'hex_color' => ':attribute muss eine gültige hexadezimale Farbe sein.',
    'image' => ':attribute muss ein Bild sein.',
    'in' => 'Das ausgewählte :attribute ist ungültig.',
    'in_array' => 'Der gewählte Wert für :attribute kommt nicht in :other vor.',
    'integer' => ':attribute muss eine ganze Zahl sein.',
    'ip' => ':attribute muss eine gültige IP-Adresse sein.',
    'ipv4' => ':attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6' => ':attribute muss eine gültige IPv6-Adresse sein.',
    'json' => ':attribute muss ein gültiger JSON-String sein.',
    'list' => ':attribute muss eine Liste sein.',
    'lowercase' => ':attribute muss in Kleinbuchstaben sein.',
    'lt' => [
        'array' => ':attribute muss weniger als :value Elemente haben.',
        'file' => ':attribute muss kleiner als :value Kilobytes sein.',
        'numeric' => ':attribute muss kleiner als :value sein.',
        'string' => ':attribute muss kürzer als :value Zeichen sein.',
    ],
    'lte' => [
        'array' => ':attribute darf maximal :value Elemente haben.',
        'file' => ':attribute muss kleiner als :value Kilobytes sein.',
        'numeric' => ':attribute muss kleiner als :value sein.',
        'string' => ':attribute darf maximal :value Zeichen lang sein.',
    ],
    'mac_address' => 'Der Wert muss eine gültige MAC-Adresse sein.',
    'max' => [
        'array' => ':attribute darf maximal :max Elemente haben.',
        'file' => ':attribute darf maximal :max Kilobytes groß sein.',
        'numeric' => ':attribute darf maximal :max sein.',
        'string' => ':attribute darf maximal :max Zeichen haben.',
    ],
    'max_digits' => ':attribute darf maximal :max Ziffern lang sein.',
    'mimes' => ':attribute muss den Dateityp :values haben.',
    'mimetypes' => ':attribute muss den Dateityp :values haben.',
    'min' => [
        'array' => ':attribute muss mindestens :min Elemente haben.',
        'file' => ':attribute muss mindestens :min Kilobytes groß sein.',
        'numeric' => ':attribute muss mindestens :min sein.',
        'string' => ':attribute muss mindestens :min Zeichen lang sein.',
    ],
    'min_digits' => ':attribute muss mindestens :min Ziffern lang sein.',
    'missing' => 'Das :attribute muss abgelehnt werden.',
    'missing_if' => 'Das :attribute muss fehlen, wenn :other gleich :value ist.',
    'missing_unless' => 'Das :attribute muss fehlen, es sei denn, :other ist :value.',
    'missing_with' => 'Das „:attribute“ muss fehlen, wenn „:values“ vorhanden ist.',
    'missing_with_all' => 'The :attribute field must be missing when :values are present.',
    'multiple_of' => 'The :attribute field must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute field format is invalid.',
    'numeric' => 'The :attribute field must be a number.',
    'password' => [
        'letters' => 'The :attribute field must contain at least one letter.',
        'mixed' => 'The :attribute field must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute field must contain at least one number.',
        'symbols' => 'The :attribute field must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present' => 'The :attribute field must be present.',
    'present_if' => 'The :attribute field must be present when :other is :value.',
    'present_unless' => 'The :attribute field must be present unless :other is :value.',
    'present_with' => 'The :attribute field must be present when :values is present.',
    'present_with_all' => 'The :attribute field must be present when :values are present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'The :attribute field format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute field must match :other.',
    'size' => [
        'array' => 'The :attribute field must contain :size items.',
        'file' => 'The :attribute field must be :size kilobytes.',
        'numeric' => 'The :attribute field must be :size.',
        'string' => 'The :attribute field must be :size characters.',
    ],
    'starts_with' => 'The :attribute field must start with one of the following: :values.',
    'string' => 'The :attribute field must be a string.',
    'timezone' => 'The :attribute field must be a valid timezone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'uppercase' => 'The :attribute field must be uppercase.',
    'url' => 'The :attribute field must be a valid URL.',
    'ulid' => 'The :attribute field must be a valid ULID.',
    'uuid' => 'The :attribute field must be a valid UUID.',

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
