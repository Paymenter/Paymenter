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

    'accepted' => 'A(z) :attribute mezőt el kell fogadni.',
    'accepted_if' => 'A(z) :attribute mezőt el kell fogadni, ha a(z) :other értéke :value.',
    'active_url' => 'A(z) :attribute mezőnek érvényes URL-nek kell lennie.',
    'after' => 'A(z) :attribute mezőnek a(z) :date utáni dátumnak kell lennie.',
    'after_or_equal' => 'A(z) :attribute mezőnek a(z) :date utáni vagy azzal megegyező dátumnak kell lennie.',
    'alpha' => 'A(z) :attribute mező csak betűket tartalmazhat.',
    'alpha_dash' => 'A(z) :attribute mező csak betűket, számokat, kötőjeleket és alulvonásokat tartalmazhat.',
    'alpha_num' => 'A(z) :attribute mező csak betűket és számokat tartalmazhat.',
    'array' => 'A(z) :attribute mezőnek tömbnek kell lennie.',
    'ascii' => 'A(z) :attribute mező csak egy bájtos alfanumerikus karaktereket és szimbólumokat tartalmazhat.',
    'before' => 'A(z) :attribute mezőnek a(z) :date előtti dátumnak kell lennie.',
    'before_or_equal' => 'A(z) :attribute mezőnek a(z) :date előtti vagy azzal megegyező dátumnak kell lennie.',
    'between' => [
        'array' => 'A(z) :attribute mezőnek :min és :max elem között kell tartalmaznia.',
        'file' => 'A(z) :attribute mező méretének :min és :max kilobájt között kell lennie.',
        'numeric' => 'A(z) :attribute mező értékének :min és :max között kell lennie.',
        'string' => 'A(z) :attribute mezőnek :min és :max karakter között kell lennie.',
    ],
    'boolean' => 'A(z) :attribute mező értéke igaz vagy hamis kell legyen.',
    'can' => 'A(z) :attribute mező jogosulatlan értéket tartalmaz.',
    'confirmed' => 'A(z) :attribute mező megerősítése nem egyezik.',
    'current_password' => 'A jelszó helytelen.',
    'date' => 'A(z) :attribute mezőnek érvényes dátumnak kell lennie.',
    'date_equals' => 'A(z) :attribute mezőnek a(z) :date-nek megfelelő dátumnak kell lennie.',
    'date_format' => 'A(z) :attribute mezőnek meg kell felelnie a(z) :format formátumnak.',
    'decimal' => 'A(z) :attribute mezőnek :decimal tizedesjegyet kell tartalmaznia.',
    'declined' => 'A(z) :attribute mezőt el kell utasítani.',
    'declined_if' => 'The :attribute field must be declined when :other is :value.',
    'different' => 'A(z) :attribute mezőnek és a(z) :other mezőnek különböznie kell.',
    'digits' => 'A(z) :attribute mezőnek :digits számjegyet kell tartalmaznia.',
    'digits_between' => 'A(z) :attribute mezőnek :min és :max számjegy között kell lennie.',
    'dimensions' => 'A(z) :attribute mező képének mérete érvénytelen.',
    'distinct' => 'A(z) :attribute mező értéke már létezik.',
    'doesnt_end_with' => 'A(z) :attribute mező nem végződhet az alábbi értékek egyikével: :values.',
    'doesnt_start_with' => 'A(z) :attribute mező nem kezdődhet az alábbi értékek egyikével: :values.',
    'email' => 'A(z) :attribute mezőnek érvényes email címnek kell lennie.',
    'ends_with' => 'A(z) :attribute mező az alábbi értékek egyikével kell, hogy végződjön: :values.',
    'enum' => 'A kiválasztott :attribute érvénytelen.',
    'exists' => 'A kiválasztott :attribute érvénytelen.',
    'extensions' => 'A(z) :attribute mezőnek az alábbi kiterjesztések egyikével kell rendelkeznie: :values.',
    'file' => 'A(z) :attribute mezőnek fájlnak kell lennie.',
    'filled' => 'A(z) :attribute mezőnek értéket kell tartalmaznia.',
    'gt' => [
        'array' => 'A(z) :attribute mezőnek több mint :value elemet kell tartalmaznia.',
        'file' => 'A(z) :attribute mezőnek nagyobbnak kell lennie, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute mező értékének nagyobbnak kell lennie, mint :value.',
        'string' => 'A(z) :attribute mezőnek több mint :value karakterből kell állnia.',
    ],
    'gte' => [
        'array' => 'A(z) :attribute mezőnek legalább :value elemet kell tartalmaznia.',
        'file' => 'A(z) :attribute mezőnek legalább :value kilobájt nagyságúnak kell lennie.',
        'numeric' => 'A(z) :attribute mező értékének legalább :value-nak kell lennie.',
        'string' => 'A(z) :attribute mezőnek legalább :value karakterből kell állnia.',
    ],
    'hex_color' => 'A(z) :attribute mezőnek érvényes hexadecimális színkódot kell tartalmaznia.',
    'image' => 'A(z) :attribute mezőnek képnek kell lennie.',
    'in' => 'A kiválasztott :attribute érvénytelen.',
    'in_array' => 'A(z) :attribute mezőnek léteznie kell a(z) :other-ban.',
    'integer' => 'A(z) :attribute mezőnek egész számnak kell lennie.',
    'ip' => 'A(z) :attribute mezőnek érvényes IP címnek kell lennie.',
    'ipv4' => 'A(z) :attribute mezőnek érvényes IPv4 címnek kell lennie.',
    'ipv6' => 'A(z) :attribute mezőnek érvényes IPv6 címnek kell lennie.',
    'json' => 'A(z) :attribute mezőnek érvényes JSON karakterláncnak kell lennie.',
    'list' => 'A(z) :attribute mezőnek listának kell lennie.',
    'lowercase' => 'A(z) :attribute mezőnek kisbetűsnek kell lennie.',
    'lt' => [
        'array' => 'A(z) :attribute mezőnek kevesebb, mint :value elemet kell tartalmaznia.',
        'file' => 'A(z) :attribute mezőnek kisebbnek kell lennie, mint :value kilobájt.',
        'numeric' => 'A(z) :attribute mező értékének kisebbnek kell lennie, mint :value.',
        'string' => 'A(z) :attribute mezőnek kevesebb, mint :value karakterből kell állnia.',
    ],
    'lte' => [
        'array' => 'A(z) :attribute mezőben nem lehet több, mint :value elem.',
        'file' => 'A(z) :attribute mezőnek legfeljebb :value kilobájt nagyságúnak kell lennie.',
        'numeric' => 'A(z) :attribute mező értéke legfeljebb :value lehet.',
        'string' => 'A(z) :attribute mező legfeljebb :value karakterből állhat.',
    ],
    'mac_address' => 'A(z) :attribute mezőnek érvényes MAC-címnek kell lennie.',
    'max' => [
        'array' => 'A(z) :attribute mezőben nem lehet több, mint :max elem.',
        'file' => 'A(z) :attribute mező mérete nem lehet nagyobb, mint :max kilobájt.',
        'numeric' => 'A(z) :attribute mező értéke nem lehet nagyobb, mint :max.',
        'string' => 'A(z) :attribute mező legfeljebb :max karakter hosszú lehet.',
    ],
    'max_digits' => 'A(z) :attribute mező nem tartalmazhat több, mint :max számjegyet.',
    'mimes' => 'A(z) :attribute mezőnek az alábbi típusú fájlnak kell lennie: :values.',
    'mimetypes' => 'A(z) :attribute mezőnek az alábbi típusú fájlnak kell lennie: :values.',
    'min' => [
        'array' => 'A(z) :attribute mezőnek legalább :min elemet kell tartalmaznia.',
        'file' => 'A(z) :attribute mezőnek legalább :min kilobájt nagyságúnak kell lennie.',
        'numeric' => 'A(z) :attribute mező értékének legalább :min-nak kell lennie.',
        'string' => 'A(z) :attribute mezőnek legalább :min karakterből kell állnia.',
    ],
    'min_digits' => 'A(z) :attribute mezőnek legalább :min számjegyből kell állnia.',
    'missing' => 'A(z) :attribute mezőnek hiányzónak kell lennie.',
    'missing_if' => 'A(z) :attribute mezőnek hiányzónak kell lennie, ha a(z) :other értéke :value.',
    'missing_unless' => 'A(z) :attribute mezőnek hiányzónak kell lennie, kivéve ha a(z) :other értéke :value.',
    'missing_with' => 'A(z) :attribute mezőnek hiányzónak kell lennie, ha a(z) :values jelen van.',
    'missing_with_all' => 'A(z) :attribute mezőnek hiányzónak kell lennie, ha a(z) :values mezők jelen vannak.',
    'multiple_of' => 'A(z) :attribute mező értékének a(z) :value többszörösének kell lennie.',
    'not_in' => 'A kiválasztott :attribute érvénytelen.',
    'not_regex' => 'A(z) :attribute mező formátuma érvénytelen.',
    'numeric' => 'A(z) :attribute mező értékének számnak kell lennie.',
    'password' => [
        'letters' => 'A(z) :attribute mezőnek legalább egy betűt kell tartalmaznia.',
        'mixed' => 'A(z) :attribute mezőnek legalább egy nagybetűt és egy kisbetűt kell tartalmaznia.',
        'numbers' => 'A(z) :attribute mezőnek legalább egy számjegyet kell tartalmaznia.',
        'symbols' => 'A(z) :attribute mezőnek legalább egy szimbólumot kell tartalmaznia.',
        'uncompromised' => 'A megadott :attribute szerepelt egy adatvédelmi incidensben. Kérjük, válasszon másik :attribute értéket.',
    ],
    'present' => 'A(z) :attribute mezőnek jelen kell lennie.',
    'present_if' => '„A(z) :attribute mezőnek jelen kell lennie, ha a(z) :other értéke :value.',
    'present_unless' => 'A(z) :attribute mezőnek jelen kell lennie, kivéve ha a(z) :other értéke :value.',
    'present_with' => 'A(z) :attribute mezőnek jelen kell lennie, ha a(z) :values mezők jelen vannak.',
    'present_with_all' => 'A(z) :attribute mezőnek jelen kell lennie, ha a(z) :values mezők jelen vannak.',
    'prohibited' => 'A(z) :attribute mező használata nem engedélyezett.',
    'prohibited_if' => 'A(z) :attribute mező használata nem engedélyezett, ha a(z) :other értéke :value.',
    'prohibited_unless' => 'A(z) :attribute mező használata nem engedélyezett, kivéve ha a(z) :other értéke szerepel a(z) :values között.',
    'prohibits' => 'A(z) :attribute mező kizárja a(z) :other mező jelenlétét.',
    'regex' => 'A(z) :attribute mező formátuma érvénytelen.',
    'required' => 'A(z) :attribute mező megadása kötelező.',
    'required_array_keys' => 'A(z) :attribute mezőnek tartalmaznia kell bejegyzéseket a következő értékekhez: :values.',
    'required_if' => 'A(z) :attribute mező megadása kötelező, ha a(z) :other értéke :value.',
    'required_if_accepted' => 'A(z) :attribute mező megadása kötelező, ha a(z) :other értéke elfogadott.',
    'required_unless' => 'A(z) :attribute mező megadása kötelező, kivéve ha a(z) :other értéke szerepel a(z) :values között.',
    'required_with' => 'A(z) :attribute mező megadása kötelező, ha a(z) :values jelen van.',
    'required_with_all' => 'A(z) :attribute mező megadása kötelező, ha a(z) :values mezők jelen vannak.',
    'required_without' => 'A(z) :attribute mező megadása kötelező, ha a(z) :values mezők jelen vannak.',
    'required_without_all' => 'A(z) :attribute mező megadása kötelező, ha a(z) :values mezők egyike sincs jelen.',
    'same' => 'A(z) :attribute mezőnek meg kell egyeznie a(z) :other mezővel.',
    'size' => [
        'array' => 'A(z) :attribute mezőnek :size elemet kell tartalmaznia.',
        'file' => 'A(z) :attribute mezőnek :size kilobájt méretűnek kell lennie.',
        'numeric' => 'A(z) :attribute mezőnek :size értékűnek kell lennie.',
        'string' => 'A(z) :attribute mezőnek :size karakter hosszúságúnak kell lennie.',
    ],
    'starts_with' => 'A(z) :attribute mezőnek a következők egyikével kell kezdődnie: :values.',
    'string' => 'A(z) :attribute mezőnek karakterláncnak (szövegnek) kell lennie.',
    'timezone' => 'A(z) :attribute mezőnek érvényes időzónának kell lennie.',
    'unique' => 'A(z) :attribute már foglalt.',
    'uploaded' => 'A(z) :attribute feltöltése sikertelen volt.',
    'uppercase' => 'A(z) :attribute mezőnek nagybetűsnek kell lennie.',
    'url' => 'A(z) :attribute mezőnek érvényes URL-nek kell lennie.',
    'ulid' => 'A(z) :attribute mezőnek érvényes ULID-nek kell lennie.',
    'uuid' => 'A(z) :attribute mezőnek érvényes UUID-nek kell lennie.',

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
            'rule-name' => 'egyéni üzenet',
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
