<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Traduction de la validation des formulaires
    |--------------------------------------------------------------------------
    |
    | Les traductions suivantes contiennent les messages d'erreur par défaut utilisés par
    | la classe du validateur. Certaines de ces règles ont plusieurs versions telles que
    | les règles de taille. N'hésitez pas à modifier chacun de ces messages ici.
    |
    */

    'accepted' => 'Le champ :attribute doit être accepté.',
    'active_url' => 'Le champ :attribute n\'est pas une URL valide.',
    'after' => 'Le champ :attribute doit être une date postérieure à :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale à :date.',
    'alpha' => 'Le champ :attribute ne peut contenir que des lettres.',
    'alpha_dash' => 'Le champ :attribute ne peut contenir que des lettres, des chiffres, des tirets et des traits de soulignement.',
    'alpha_num' => 'Le champ :attribute ne peut contenir que des lettres et des chiffres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'before' => 'Le champ :attribute doit être une date antérieure à :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    'between' => [
                'numeric' => 'La valeur du champ :attribute doit être comprise entre :min et :max.',
                'file' => 'La taille du fichier :attribute doit être comprise entre :min et :max kilo-octets.',
                'string' => 'Le champ :attribute doit contenir entre :min et :max caractères.',
                'array' => 'Le champ :attribute doit contenir entre :min et :max éléments.',
    ],
        'boolean' => 'Le champ :attribute doit être vrai ou faux.',
        'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
        'date' => 'Le champ :attribute n\'est pas une date valide.',
        'date_equals' => 'Le champ :attribute doit être égal à la date :date.',
        'date_format' => 'Le champ :attribute ne correspond pas au format :format.',
        'different' => 'Le champ :attribute doit être différent du champ :other.',
        'digits' => 'Le champ :attribute doit contenir :digits chiffres.',
        'digits_between' => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
        'dimensions' => 'Les dimensions de l\'image :attribute ne sont pas valides.',
        'distinct' => 'La valeur du champ :attribute est dupliquée.',
        'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
        'ends_with' => 'Le champ :attribute doit se terminer par l\'une des valeurs suivantes : :values.',
        'exists' => 'La valeur sélectionnée pour le champ :attribute est invalide.',
        'file' => 'Le champ :attribute doit être un fichier.',
        'filled' => 'Le champ :attribute doit avoir une valeur.',
    'gt' => [
            'numeric' => 'La valeur du champ :attribute doit être supérieure à :value.',
            'file' => 'La taille du fichier :attribute doit être supérieure à :value kilo-octets.',
            'string' => 'Le champ :attribute doit contenir plus de :value caractères.',
            'array' => 'Le champ :attribute doit contenir plus de :value éléments.',
    ],
    'gte' => [
        'numeric' => 'La valeur du champ :attribute doit être supérieure ou égale à :value.',
        'file' => 'La taille du fichier :attribute doit être supérieure ou égale à :value kilo-octets.',
        'string' => 'Le champ :attribute doit contenir au moins :value caractères.',
        'array' => 'Le champ :attribute doit contenir :value éléments ou plus.',
    ],
    'image' => 'Le champ :attribute doit être une image.',
    'in' => 'La valeur sélectionnée pour le champ :attribute est invalide.',
    'in_array' => 'La valeur du champ :attribute n\'existe pas dans :other.',
    'integer' => 'Le champ :attribute doit être un entier.',
    'ip' => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6' => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json' => 'Le champ :attribute doit être une chaîne JSON valide.',
    'lt' => [
        'numeric' => 'La valeur du champ :attribute doit être inférieure à :value.',
        'file' => 'La taille du fichier :attribute doit être inférieure à :value kilo-octets.',
        'string' => 'Le champ :attribute doit contenir moins de :value caractères.',
        'array' => 'Le champ :attribute doit contenir moins de :value éléments.',
    ],
    'lte' => [
        'numeric' => 'La valeur du champ :attribute doit être inférieure ou égale à :value.',
        'file' => 'La taille du fichier :attribute doit être inférieure ou égale à :value kilo-octets.',
        'string' => 'Le champ :attribute doit contenir au maximum :value caractères.',
        'array' => 'Le champ :attribute ne doit pas contenir plus de :value éléments.',
    ],
    'max' => [
        'numeric' => 'La valeur du champ :attribute ne peut pas être supérieure à :max.',
        'file' => 'La taille du fichier :attribute ne peut pas être supérieure à :max kilo-octets.',
        'string' => 'Le champ :attribute ne peut pas contenir plus de :max caractères.',
        'array' => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
    ],
    'mimes' => 'Le champ :attribute doit être un fichier de type :values.',
    'mimetypes' => 'Le champ :attribute doit être un fichier de type :values.',
    'min' => [
        'numeric' => 'La valeur du champ :attribute doit être au moins égale à :min.',
        'file' => 'La taille du fichier :attribute doit être au moins égale à :min kilo-octets.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
    ],
    'multiple_of' => 'Le champ :attribute doit être un multiple de :value.',
    'not_in' => 'La valeur sélectionnée pour le champ :attribute est invalide.',
    'not_regex' => 'Le format du champ :attribute est invalide.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'password' => 'Le mot de passe est incorrect.',
    'present' => 'Le champ :attribute doit être présent.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est requis.',
    'required_if' => 'Le champ :attribute est requis lorsque :other a la valeur :value.',
    'required_unless' => 'Le champ :attribute est requis sauf si :other est dans :values.',
    'required_with' => 'Le champ :attribute est requis lorsque :values est présent.',
    'required_with_all' => 'Le champ :attribute est requis lorsque :values sont présents.',
    'required_without' => 'Le champ :attribute est requis lorsque :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est requis lorsque aucun des champs :values n\'est présent.',
    'same' => 'Le champ :attribute doit correspondre au champ :other.',
    'size' => [
        'numeric' => 'La valeur du champ :attribute doit être égale à :size.',
        'file' => 'La taille du fichier :attribute doit être égale à :size kilo-octets.',
        'string' => 'Le champ :attribute doit contenir :size caractères.',
        'array' => 'Le champ :attribute doit contenir :size éléments.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer par l\'une des valeurs suivantes : :values.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique' => 'La valeur du champ :attribute est déjà utilisée.',
    'uploaded' => 'Le téléchargement du champ :attribute a échoué.',
    'url' => 'Le format du champ :attribute est invalide.',
    'uuid' => 'Le champ :attribute doit être un UUID valide.',

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
