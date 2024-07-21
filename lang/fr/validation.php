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

    'accepted' => ':attribute doit être accepté.',
    'active_url' => ":attribute n'est pas une URL valide.",
    'after' => ':attribute doit être une date après :date.',
    'after_or_equal' => ':attribute doit être une date superieur ou égale à :date.',
    'alpha' => ':attribute ne peut contenir que des lettres.',
    'alpha_dash' => ':attribute ne peut contenir que des lettres, des chiffres, des tirets et des traits de soulignement.',
    'alpha_num' => ':attribute ne peut contenir que des lettres et des chiffres.',
    'array' => ':attribute doit être un array.',
    'before' => ':attribute doit être une date avant :date.',
    'before_or_equal' => ':attribute doit être une date antérieure ou égale à :date.',
    'between' => [
        'numeric' => ':attribute doit être entre :min et :max.',
        'file' => ':attribute doit être entre :min et :max kilobytes.',
        'string' => ':attribute doit être entre :min et :max characters.',
        'array' => ':attribute doit être entre :min et :max items.',
    ],
    'boolean' => ':attribute le champ doit être vrai ou faux.',
    'confirmed' => ':attribute la confirmation ne correspond pas.',
    'date' => "Le :attribute Ce n'est pas une date valide.",
    'date_equals' => ':attribute doit être une date égale à :date.',
    'date_format' => ':attribute ne correspond pas au format :format.',
    'different' => ':attribute et :other doit être différent.',
    'digits' => ':attribute doit être :digits digits.',
    'digits_between' => ':attribute doit être entre :min et :max digits.',
    'dimensions' => ":attribute les dimensions de l'image ne sont pas valides.",
    'distinct' => ':attribute le champ a une valeur en double.',
    'email' => ':attribute doit être une adresse e-mail valide.',
    'ends_with' => ":attribute doit se terminer par l'un des éléments suivants: :values.",
    'exists' => 'Le sélectionné :attribute est invalide.',
    'file' => ':attribute ça doit être un fichier.',
    'filled' => ':attribute le champ doit avoir une valeur.',
    'gt' => [
        'numeric' => ':attribute doit être supérieur à :value.',
        'file' => ':attribute doit être supérieur à :value kilobytes.',
        'string' => ':attribute doit être supérieur à :value characters.',
        'array' => ':attribute doit avoir plus de :value items.',
    ],
    'gte' => [
        'numeric' => ':attribute doit être supérieur ou à égal :value.',
        'file' => ':attribute doit être supérieur ou à égal :value kilobytes.',
        'string' => ':attribute doit être supérieur ou à égal :value characters.',
        'array' => ':attribute doit avoir :value items ou plus.',
    ],
    'image' => ':attribute ça doit être une image.',
    'in' => 'Le sélectionné :attribute est invalide.',
    'in_array' => ":attribute le champ n'existe pas dans :other.",
    'integer' => ':attribute must be an integer.',
    'ip' => ':attribute doit être une adresse IP valide.',
    'ipv4' => ':attribute doit être une adresse IPv4 valide.',
    'ipv6' => ':attribute doit être une adresse IPv6 valide.',
    'json' => ':attribute doit être une chaîne JSON valide.',
    'lt' => [
        'numeric' => ':attribute doit être inférieur à :value.',
        'file' => ':attribute doit être inférieur à :value kilobytes.',
        'string' => ':attribute doit être inférieur à :value characters.',
        'array' => ':attribute doit avoir moins de :value items.',
    ],
    'lte' => [
        'numeric' => ':attribute doit être inférieur ou égal :value.',
        'file' => ':attribute doit être inférieur ou égal :value kilobytes.',
        'string' => ':attribute doit être inférieur ou égal :value characters.',
        'array' => ':attribute ne doit pas avoir plus de :value items.',
    ],
    'max' => [
        'numeric' => ':attribute ne peut pas être supérieur à :max.',
        'file' => ':attribute ne peut pas être supérieur à :max kilobytes.',
        'string' => ':attribute ne peut pas être supérieur à :max characters.',
        'array' => ':attribute ne peut pas avoir plus de :max items.',
    ],
    'mimes' => ':attribute : doit être un fichier de type: :values.',
    'mimetypes' => ':attribute doit être un fichier de type: :values.',
    'min' => [
        'numeric' => ':attribute doit être au moins :min.',
        'file' => ':attribute doit être au moins :min kilobytes.',
        'string' => ':attribute doit être au moins :min characters.',
        'array' => ':attribute doit avoir au moins :min items.',
    ],
    'multiple_of' => ':attribute doit être un multiple de :value.',
    'not_in' => 'Le sélectionné :attribute est invalide.',
    'not_regex' => ":attribute le format n'est pas valide.",
    'numeric' => ':attribute doit être un nombre.',
    'password' => 'Le mot de passe est incorrect.',
    'present' => ':attribute le champ doit être présent.',
    'regex' => ":attribute le format n'est pas valide.",
    'required' => ':attribute Champ requis.',
    'required_if' => ':attribute ce champ est obligatoire lorsque :other est :value.',
    'required_unless' => ':attribute Le champ est obligatoire sauf si :other est dans :values.',
    'required_with' => ':attribute ce champ est obligatoire lorsque :values est présent.',
    'required_with_all' => ':attribute ce champ est obligatoire lorsque :values est présent.',
    'required_without' => ":attribute ce champ est obligatoire lorsque :values n'est pas présent.",
    'required_without_all' => ":attribute Ce champ est obligatoire lorsqu'aucun des :values est présent.",
    'same' => ':attribute et :other doit correspondre.',
    'size' => [
        'numeric' => ':attribute doit être :size.',
        'file' => ':attribute doit être :size kilobytes.',
        'string' => ':attribute doit être :size characters.',
        'array' => ':attribute doit contenir :size items.',
    ],
    'starts_with' => ":attribute doit commencer par l'un des éléments suivants: :values.",
    'string' => ':attribute doit être un string.',
    'timezone' => ':attribute doit être une zone valide.',
    'unique' => ':attribute a déjà été pris.',
    'uploaded' => ':attribute échec du téléchargement.',
    'url' => ":attribute le format n'est pas valide.",
    'uuid' => ':attribute doit être un UUID valide.',

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
