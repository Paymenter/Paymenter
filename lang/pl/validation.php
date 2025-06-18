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

    'accepted' => 'Pole :attribute musi być zaakceptowane.',
    'accepted_if' => 'Pole :attribute musi być zaakceptowane, gdy :other jest :value.',
    'active_url' => 'Pole :attribute musi zawierać prawidłowy URL.',
    'after' => 'Pole :attribute musi być datą po :date.',
    'after_or_equal' => 'To :attribute pole musi być data po lub równa :date.',
    'alpha' => 'Pole :attribute może zawierać jedynie litery.',
    'alpha_dash' => 'Pole :attribute może zawierać jedynie litery, cyfry, myślniki i podkreślenia.',
    'alpha_num' => 'To :attribute pole musi zawierać tylko litery i cyfry.',
    'array' => 'Pole :attribute może być jedynie zbiorem.',
    'ascii' => 'To :attribute pole musi zawierać tylko jednobajtowe znaki alfanumeryczne i symbole.',
    'before' => ':attribute musi być datą wcześniejszą niż :date.',
    'before_or_equal' => 'Pole :attribute musi być datą wcześniejszą lub tą samą co :date.',
    'between' => [
        'array' => 'To :attribute pole musi być pomiędzy :min and :max pozycje.',
        'file' => 'Pole :attribute musi zawierać się w przedziale od :min do :max kilobajtów.',
        'numeric' => 'Pole :attribute musi zawierać się pomiędzy :min a :max.',
        'string' => 'Pole :attribute musi zawierać pomiędzy :min a :max znaków.',
    ],
    'boolean' => ':attribute musi mieć wartość prawda albo fałsz.',
    'can' => 'Pole :attribute zawiera nieautoryzowaną wartość.',
    'confirmed' => 'Potwierdzenie :attribute nie pasuje.',
    'current_password' => 'Hasło jest nieprawidłowe.',
    'date' => 'Pole :attribute musi zawierać prawidłową datę.',
    'date_equals' => ':attribute musi być datą równą :date.',
    'date_format' => 'Pole :attribute musi być zgodne z formatem :format.',
    'decimal' => ':attribute musi mieć :decimal miejsc dziesiętnych.',
    'declined' => 'To :attribute pole musi zostać odrzucone.',
    'declined_if' => 'Pole :attribute musi zostać odrzucone, gdy :other jest :value.',
    'different' => ':attribute i :other muszą być różne.',
    'digits' => 'To :attribute pole musi być :digits digits.',
    'digits_between' => 'Pole :attribute musi zawierać się między :min a :max cyframi.',
    'dimensions' => ':attribute ma nieprawidłowe wymiary obrazu.',
    'distinct' => 'Pole :attribute zawiera zduplikowaną wartość.',
    'doesnt_end_with' => ':attribute nie może kończyć się jednym z następujących elementów: :values.',
    'doesnt_start_with' => ':attribute nie może kończyć się jednym z następujących elementów: :values.',
    'email' => 'Pole :attribute musi zawierać prawidłową datę.',
    'ends_with' => 'Pole :attribute musi kończyć się jedną z następujących :values.',
    'enum' => 'Zaznaczony :attribute jest nieprawidłowy.',
    'exists' => 'Zaznaczony :attribute jest nieprawidłowy.',
    'extensions' => 'To :attribute pole musi mieć jedno z następujących rozszerzeń: :values.',
    'file' => ':attribute musi być plikiem.',
    'filled' => 'Pole :attribute nie może być puste.',
    'gt' => [
        'array' => 'To :attribute pole musi mieć więcej niż :value pozycji.',
        'file' => 'Rozmiar pliku dla :attribute musi być większy niż :value kilobajtów.',
        'numeric' => 'To :attribute pole musi być większe niż :value.',
        'string' => 'To :attribute pole musi być większa niż :value znaki.',
    ],
    'gte' => [
        'array' => ':attribute musi mieć :value elementów lub więcej.',
        'file' => 'To :attribute pole musi być większe lub równe :value kilobajty.',
        'numeric' => ':attribute musi być równy lub większy niż :value.',
        'string' => 'Pole :attribute musi być większe lub równe :value znakom.',
    ],
    'hex_color' => 'Pole :attribute musi mieć podany prawidłowy kolor w formacie szesnastkowym.',
    'image' => ':attribute musi być obrazem.',
    'in' => 'Zaznaczony :attribute jest nieprawidłowy.',
    'in_array' => 'Pole :attribute nie istnieje w :other.',
    'integer' => 'To :attribute pole musi być liczbą całkowitą.',
    'ip' => 'Pole :attribute musi być poprawnym adresem IP.',
    'ipv4' => 'Pole :attribute musi być poprawnym adresem IPv4.',
    'ipv6' => ':attribute musi być prawidłowym adresem IPv6.',
    'json' => ':attribute musi być prawidłowym łańcuchem JSON.',
    'list' => ':attribute musi być plikiem.',
    'lowercase' => 'To :attribute pole musi być małymi literami.',
    'lt' => [
        'array' => ':attribute musi mieć mniej niż :value elementów.',
        'file' => 'To :attribute pole musi być mniej niż :value kilobajty.',
        'numeric' => 'To :attribute pole musi być mniej niż :value.',
        'string' => ':attribute musi mieć mniej niż :value znaków.',
    ],
    'lte' => [
        'array' => ':attribute nie może mieć więcej niż :value elementów.',
        'file' => 'To :attribute pole musi być mniej lub równa niż :value kilobajty.',
        'numeric' => ':attribute musi być mniejszy lub równy :value.',
        'string' => 'Pole :attribute musi być mniejsze lub równe :value znakom.',
    ],
    'mac_address' => 'To :attribute pole musi być prawidłowym adresem MAC.',
    'max' => [
        'array' => ':attribute nie może mieć więcej niż :max elementów.',
        'file' => 'To :pole atrybutu nie może być większe niż :max kilobajty.',
        'numeric' => 'To :attribute pole nie może być większe niż :max.',
        'string' => ':attribute nie może mieć więcej niż :max znaków.',
    ],
    'max_digits' => ':attribute nie może mieć więcej niż :max cyfr.',
    'mimes' => 'Pole :attribute musi być plikiem typu: :values.',
    'mimetypes' => 'Pole :attribute musi być plikiem typu: :values.',
    'min' => [
        'array' => 'To :attribute pole musi mieć co najmniej :min pozycji.',
        'file' => 'To :attribute pole musi być co najmniej :min kilobajty.',
        'numeric' => 'Pole :attribute musi być przynajmniej :min.',
        'string' => ':attribute musi mieć przynajmniej :min znaków.',
    ],
    'min_digits' => 'To :attribute pole musi zawierać co najmniej :min cyfry.',
    'missing' => 'Pole :attribute musi być puste.',
    'missing_if' => 'Pole :attribute musi być puste, gdy :other to :value.',
    'missing_unless' => 'Pole :attribute musi zostać puste, chyba że :other jest :value.',
    'missing_with' => 'Pole :attribute musi być puste, gdy :values jest obecny.',
    'missing_with_all' => 'To :attribute pole musi być brakujące, gdy :values są obecne.',
    'multiple_of' => ':attribute musi być wielokrotnością :value.',
    'not_in' => 'Zaznaczony :attribute jest nieprawidłowy.',
    'not_regex' => 'Format :attribute jest nieprawidłowy.',
    'numeric' => ':attribute musi być liczbą.',
    'password' => [
        'letters' => ':attribute musi zawierać przynajmniej jedną literę.',
        'mixed' => 'To :attribute pole musi zawierać co najmniej jedną wielka i jedną małą literę.',
        'numbers' => 'To :attribute pole musi zawierać co najmniej jedną cyfrę.',
        'symbols' => ':attribute musi zawierać przynajmniej jeden symbol.',
        'uncompromised' => 'Podany :attribute pojawił się w wycieku danych. Wybierz inny :attribute.',
    ],
    'present' => 'Pole :attribute musi być wypełnione.',
    'present_if' => 'Pole :attribute musi być obecne jeżeli :other ma wartość :value.',
    'present_unless' => 'To :attribute pole musi być obecne, chyba że :other is :value.',
    'present_with' => 'Pole :attribute musi być obecne kiedy :values jest podany/a.',
    'present_with_all' => 'Pole :attribute musi być obecne kiedy :values są podane.',
    'prohibited' => 'Pole :attribute jest zabronione.',
    'prohibited_if' => 'Pole :attribute jest niedozwolone gdy :other to :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'Pole :attribute zabrania :other obecności .',
    'regex' => 'Format :attribute jest nieprawidłowy.',
    'required' => 'Pole :attribute jest wymagane.',
    'required_array_keys' => 'Pole :attribute musi zawierać wartości: :values.',
    'required_if' => 'Pole :attribute jest wymagane gdy :other jest :value.',
    'required_if_accepted' => 'Pole :attribute jest wymagane, gdy akceptowane jest pole :other.',
    'required_unless' => ':attribute jest wymagany jeżeli :other nie znajduje się w :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'Pole :attribute jest wymagane gdy wszystkie :values są obecne.',
    'required_without' => 'The :attribute field is required when :values is present.',
    'required_without_all' => 'Pole :attribute jest wymagane gdy żadne z :values nie są obecne.',
    'same' => 'To :attribute pole musi odpowiadać :other.',
    'size' => [
        'array' => 'To :attribute pole musi zawierać :size pozycji.',
        'file' => 'Rozmiar pliku dla :attribute musi wynosić :size kilobajtów.',
        'numeric' => ':attribute musi być plikiem.',
        'string' => ':attribute musi mieć przynajmniej :min znaków.',
    ],
    'starts_with' => 'Pole :attribute musi zaczynać się jedną z następujących :values.',
    'string' => ':attribute musi być ciągiem znaków.',
    'timezone' => 'To :attribute pole musi być prawidłową strefą czasową.',
    'unique' => 'Taki :attribute został zajęty.',
    'uploaded' => ':attribute nie udało się przesłać.',
    'uppercase' => 'Pole :attribute musi być pisane wielkimi literami.',
    'url' => 'Pole :attribute musi zawierać prawidłowy URL.',
    'ulid' => 'To :attribute pole musi być ważnym ULID.',
    'uuid' => 'Pole :attribute musi być poprawnym identyfikatorem UUID.',

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
            'rule-name' => 'niestandardowa-wiadomość',
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
