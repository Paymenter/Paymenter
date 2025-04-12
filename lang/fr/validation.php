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

    'accepted' => 'Le champ :attribute doit être accepté.',
    'accepted_if' => 'Le champ :attribute doit être accepté lorsque :other est :value.',
    'active_url' => 'Le champ :attribute doit être une URL valide.',
    'after' => 'Le champ :attribute doit être une date après le :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha' => 'Le champ :attribute doit contenir uniquement des lettres.',
    'alpha_dash' => 'Le champ :attribute ne doit contenir que des lettres, des chiffres, des tirets et des traits de soulignement.',
    'alpha_num' => 'Le champ :attribut ne doit contenir que des lettres et des chiffres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'ascii' => 'Le champ :attribute ne doit contenir que des caractères alphanumériques et des symboles d\'un seul octet.',
    'before' => 'Le champ :attribute doit être une date antérieure à :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date avant ou égale au :date.',
    'between' => [
        'array' => 'Le champ :attribute doit comporter entre :min et :max éléments.',
        'file' => 'La taille du fichier de :attribute doit être comprise entre :min et :max kilo-octets.',
        'numeric' => 'Le champ :attribute doit être compris entre :min et :max.',
        'string' => 'Le champ :attribute doit contenir entre :min et :max caractères.',
    ],
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'can' => 'Le champ :attribute contient une valeur non autorisée.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'date_equals' => 'Le champ :attribute doit être une date égale à :date.',
    'date_format' => 'Le champ :attribute doit correspondre au format :format.',
    'decimal' => 'Le champ :attribute doit avoir :decimal chiffres après la virgule.',
    'declined' => 'Le champ :attribute doit être refusé.',
    'declined_if' => 'Le champ :attribute doit être refusé quand :other a la valeur :value.',
    'different' => 'Le champ :attribute et :other doivent être différents.',
    'digits' => 'Le champ :attribute doit être composé de :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit être compris entre :min et :max chiffres.',
    'dimensions' => 'Le champ :attribute a des dimensions d\'image non valides.',
    'distinct' => 'Le champ :attribute a une valeur dupliquée.',
    'doesnt_end_with' => 'Le champ :attribute ne doit pas se terminer avec une des valeurs suivantes : :values.',
    'doesnt_start_with' => 'Le champ :attribute ne doit pas commencer par l\'une des valeurs suivantes : :values.',
    'email' => 'Le champ :attribute doit être une adresse mail valide.',
    'ends_with' => 'Le champ :attribute doit se terminer par l\'un des éléments suivants : :values.',
    'enum' => 'Le champ :attribute sélectionné est invalide.',
    'exists' => 'Le champ :attribute sélectionné est invalide.',
    'extensions' => 'Le champ :attribute doit avoir une des extensions suivantes : :values.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'filled' => 'Le champ :attribute doit avoir une valeur.',
    'gt' => [
        'array' => 'Le champ :attribute doit avoir plus de :value objets.',
        'file' => 'La taille du fichier de :attribute doit être supérieure à :value kilo-octets.',
        'numeric' => 'La valeur du champ :attribute doit être supérieure à :value.',
        'string' => 'Le champ :attribute doit contenir plus de :value caractères.',
    ],
    'gte' => [
        'array' => 'Le champ :attribute doit contenir des éléments :value ou plus.',
        'file' => 'La taille du fichier de :attribute doit être supérieure ou égale à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :value.',
        'string' => 'Le champ :attribute doit être supérieur ou égal aux caractères :value.',
    ],
    'hex_color' => 'Le champ :attribute doit être un code couleur hexadécimal valide.',
    'image' => 'Le champ :attribute doit être une image.',
    'in' => 'Le champ :attribute sélectionné est invalide.',
    'in_array' => 'Le champ :attribute doit exister dans :other.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'ip' => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6' => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json' => 'Le champ :attribute doit être un fichier JSON valide.',
    'list' => 'Le champ :attribute doit être une liste.',
    'lowercase' => 'Le champ :attribute doit être en minuscule.',
    'lt' => [
        'array' => 'Le tableau :attribute doit contenir moins de :value éléments.',
        'file' => 'La taille du fichier de :attribute doit être inférieure à :value kilo-octets.',
        'numeric' => 'La valeur de :attribute doit être inférieure à :value.',
        'string' => 'Le texte :attribute doit contenir moins de :value caractères.',
    ],
    'lte' => [
        'array' => 'Le champ :attribute ne doit pas avoir plus de :value éléments.',
        'file' => 'Le champ :attribute doit être inférieur ou égal à :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit être inférieur ou égal à :value.',
        'string' => 'Le champ :attribute doit être inférieur ou égal à :value caractères.',
    ],
    'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
    'max' => [
        'array' => 'Le tableau :attribute ne peut pas contenir plus que :max éléments.',
        'file' => 'Le champ :attribute ne doit pas être supérieur à :max kilo-octets.',
        'numeric' => 'Le champ :attribute ne doit pas être supérieur à :max.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],
    'max_digits' => 'Le champ :attribute ne doit pas contenir plus de :max chiffres.',
    'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'min' => [
        'array' => 'Le tableau :attribute doit contenir au moins :min éléments.',
        'file' => 'Le champ :attribute doit avoir une taille minimale de :min kilo-octets.',
        'numeric' => 'Le champ :attribute doit être d\'au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'min_digits' => 'Le champ :attribute doit comporter au moins :min chiffres.',
    'missing' => 'Le champ :attribute est vide.',
    'missing_if' => 'Le champ :attribute doit être vide quand :other a la valeur :value.',
    'missing_unless' => 'Le champ :attribute doit être vide tant que :other n\'est pas :value.',
    'missing_with' => 'Le champ :attribute doit être vide lorsque :values est présent.',
    'missing_with_all' => 'Le champ :attribute doit être vide lorsque :values est présent.',
    'multiple_of' => 'Le champ :attribute doit être un multiple de :value.',
    'not_in' => 'Le champ :attribute sélectionné est invalide.',
    'not_regex' => 'Le format du champ :attribute n\'est pas valide.',
    'numeric' => 'Le champ :attribute doit contenir un nombre.',
    'password' => [
        'letters' => 'Le champ :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le champ :attribute doit contenir au moins une lettre majuscule et une lettre minuscule.',
        'numbers' => 'Le champ :attribute doit contenir au moins un chiffre.',
        'symbols' => 'Le champ :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'La valeur du champ :attribute est apparue dans une fuite de données. Veuillez choisir une valeur différente.',
    ],
    'present' => 'Le champ :attribute doit être présent.',
    'present_if' => 'Le champ :attribute doit être présent quand :other est :value.',
    'present_unless' => 'Le champ :attribute doit être présent sauf si :other est :value.',
    'present_with' => 'Le champ :attribute doit être présent quand :values est présent.',
    'present_with_all' => 'Le champ :attribute doit être présent lorsque :values sont présents.',
    'prohibited' => 'Le champ :attribute est interdit.',
    'prohibited_if' => 'Le champ :attribute est interdit lorsque :other vaut :value.',
    'prohibited_unless' => 'Le champ :attribute est interdit à moins que :other est l\'une des valeurs :values.',
    'prohibits' => 'Le champ :attribute interdit la présence de :other.',
    'regex' => 'Le format du champ :attribute n\'est pas valide.',
    'required' => 'Le champ :attribute est requis.',
    'required_array_keys' => 'Le champ :attribute doit contenir des entrées pour : :values.',
    'required_if' => 'Le champ :attribute est obligatoire quand la valeur de :other est :value.',
    'required_if_accepted' => 'Le champ :attribute est obligatoire quand le champ :other a été accepté.',
    'required_unless' => 'Le champ :attribute est requis à moins que :other ne soit en :values.',
    'required_with' => 'Le champ :attribute est requis quand :values est présent.',
    'required_with_all' => 'Le champ :attribute est obligatoire lorsque :values est présent.',
    'required_without' => 'Le champ :attribute est obligatoire quand :values n\'est pas présent.',
    'required_without_all' => 'Le champ de l\'attribut :attribute est nécessaire lorsque aucun :values n\'est présent.',
    'same' => 'Le champ :attribute doit correspondre à :other.',
    'size' => [
        'array' => 'Le tableau :attribute doit contenir :size éléments.',
        'file' => 'La taille du fichier de :attribute doit être de :size kilo-octets.',
        'numeric' => 'La valeur du champ :attribute doit être de :size.',
        'string' => 'Le champ :attribute doit être :size caractères.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer par l\'une des valeurs suivantes : :values.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique' => 'La valeur du champ :attribute est déjà utilisée.',
    'uploaded' => 'L\'envoi de :attribute à échoué.',
    'uppercase' => 'Le champ :attribute doit être en majuscules.',
    'url' => 'Le champ :attribute doit être une URL valide.',
    'ulid' => 'Le champ :attribute doit être un ULID valide.',
    'uuid' => 'Le champ :attribute doit être une UUID valide.',

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
            'rule-name' => 'message personnalisé',
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
