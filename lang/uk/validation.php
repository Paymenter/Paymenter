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

    'accepted' => 'Поле :attribute повинно бути прийнято.',
    'accepted_if' => 'Поле :attribute повинно бути прийнято, коли :other є :value.',
    'active_url' => 'Поле :attribute повинно бути дійсною URL-адресою.',
    'after' => 'Поле :attribute повинно бути датою після :date.',
    'after_or_equal' => 'Поле :attribute повинно бути датою після або рівною :date.',
    'alpha' => 'Поле :attribute повинно містити лише літери.',
    'alpha_dash' => 'Поле :attribute повинно містити лише літери, цифри, дефіси та підкреслення.',
    'alpha_num' => 'Поле :attribute повинно містити лише літери та цифри.',
    'array' => 'Поле :attribute повинно бути масивом.',
    'ascii' => 'Поле :attribute повинно містити лише одно байтові алфавітно-цифрові символи та символи.',
    'before' => 'Поле :attribute повинно бути датою перед :date.',
    'before_or_equal' => 'Поле :attribute повинно бути датою перед або рівною :date.',
    'between' => [
        'array' => 'Поле :attribute повинно мати від :min до :max елементів.',
        'file' => 'Поле :attribute повинно бути між :min і :max кілобайтами.',
        'numeric' => 'Поле :attribute повинно бути між :min і :max.',
        'string' => 'Поле :attribute повинно містити від :min до :max символів.',
    ],
    'boolean' => 'Поле :attribute повинно бути true або false.',
    'can' => 'Поле :attribute містить несанкціоноване значення.',
    'confirmed' => 'Підтвердження поля :attribute не збігається.',
    'current_password' => 'Пароль введено неправильно.',
    'date' => 'Поле :attribute повинно бути дійсною датою.',
    'date_equals' => 'Поле :attribute повинно бути датою, рівною :date.',
    'date_format' => 'Поле :attribute повинно відповідати формату :format.',
    'decimal' => 'Поле :attribute повинно мати :decimal десяткових знаків.',
    'declined' => 'Поле :attribute повинно бути відхилене.',
    'declined_if' => 'Поле :attribute повинно бути відхилене, коли :other є :value.',
    'different' => 'Поле :attribute та :other повинні бути різними.',
    'digits' => 'Поле :attribute повинно бути :digits цифрами.',
    'digits_between' => 'Поле :attribute повинно бути між :min і :max цифрами.',
    'dimensions' => 'Поле :attribute має недійсні розміри зображення.',
    'distinct' => 'Поле :attribute має повторюване значення.',
    'doesnt_end_with' => 'Поле :attribute не повинно закінчуватися на одне з наступного: :values.',
    'doesnt_start_with' => 'Поле :attribute не повинно починатися з одного з наступного: :values.',
    'email' => 'Поле :attribute повинно бути дійсною адресою електронної пошти.',
    'ends_with' => 'Поле :attribute повинно закінчуватися на одне з наступного: :values.',
    'enum' => 'Вибране :attribute є недійсним.',
    'exists' => 'Вибране :attribute є недійсним.',
    'extensions' => 'Поле :attribute повинно мати одне з наступних розширень: :values.',
    'file' => 'Поле :attribute повинно бути файлом.',
    'filled' => 'Поле :attribute повинно мати значення.',
    'gt' => [
        'array' => 'Поле :attribute повинно мати більше ніж :value елементів.',
        'file' => 'Поле :attribute повинно бути більше ніж :value кілобайт.',
        'numeric' => 'Поле :attribute повинно бути більшим за :value.',
        'string' => 'Поле :attribute повинно бути більшим за :value символів.',
    ],
    'gte' => [
        'array' => 'Поле :attribute повинно мати :value елементів або більше.',
        'file' => 'Поле :attribute повинно бути більшим або рівним :value кілобайт.',
        'numeric' => 'Поле :attribute повинно бути більшим або рівним :value.',
        'string' => 'Поле :attribute повинно бути більшим або рівним :value символів.',
    ],
    'hex_color' => 'Поле :attribute повинно бути дійсним шістнадцятковим кольором.',
    'image' => 'Поле :attribute повинно бути зображенням.',
    'in' => 'Вибране :attribute є недійсним.',
    'in_array' => 'Поле :attribute повинно існувати в :other.',
    'integer' => 'Поле :attribute повинно бути цілим числом.',
    'ip' => 'Поле :attribute повинно бути дійсною IP-адресою.',
    'ipv4' => 'Поле :attribute повинно бути дійсною адресою IPv4.',
    'ipv6' => 'Поле :attribute повинно бути дійсною адресою IPv6.',
    'json' => 'Поле :attribute повинно бути дійсним JSON рядком.',
    'list' => 'Поле :attribute повинно бути списком.',
    'lowercase' => 'Поле :attribute повинно бути в нижньому регістрі.',
    'lt' => [
        'array' => 'Поле :attribute повинно мати менше ніж :value елементів.',
        'file' => 'Поле :attribute повинно бути менше ніж :value кілобайт.',
        'numeric' => 'Поле :attribute повинно бути меншим за :value.',
        'string' => 'Поле :attribute повинно бути меншим за :value символів.',
    ],
    'lte' => [
        'array' => 'Поле :attribute не повинно мати більше ніж :value елементів.',
        'file' => 'Поле :attribute повинно бути менше або рівним :value кілобайт.',
        'numeric' => 'Поле :attribute повинно бути меншим або рівним :value.',
        'string' => 'Поле :attribute повинно бути меншим або рівним :value символів.',
    ],
    'mac_address' => 'Поле :attribute повинно бути дійсною MAC-адресою.',
    'max' => [
        'array' => 'Поле :attribute не повинно мати більше ніж :max елементів.',
        'file' => 'Поле :attribute не повинно перевищувати :max кілобайт.',
        'numeric' => 'Поле :attribute не повинно перевищувати :max.',
        'string' => 'Поле :attribute не повинно перевищувати :max символів.',
    ],
    'max_digits' => 'Поле :attribute не повинно мати більше ніж :max цифр.',
    'mimes' => 'Поле :attribute повинно бути файлом типу: :values.',
    'mimetypes' => 'Поле :attribute повинно бути файлом типу: :values.',
    'min' => [
        'array' => 'Поле :attribute повинно мати щонайменше :min елементів.',
        'file' => 'Поле :attribute повинно бути принаймні :min кілобайт.',
        'numeric' => 'Поле :attribute повинно бути принаймні :min.',
        'string' => 'Поле :attribute повинно містити принаймні :min символів.',
    ],
    'min_digits' => 'Поле :attribute повинно мати щонайменше :min цифр.',
    'missing' => 'Поле :attribute повинно бути відсутнім.',
    'missing_if' => 'Поле :attribute повинно бути відсутнім, коли :other є :value.',
    'missing_unless' => 'Поле :attribute повинно бути відсутнім, якщо :other не є :value.',
    'missing_with' => 'Поле :attribute повинно бути відсутнім, коли :values присутні.',
    'missing_with_all' => 'Поле :attribute повинно бути відсутнім, коли :values присутні.',
    'multiple_of' => 'Поле :attribute повинно бути кратним :value.',
    'not_in' => 'Вибране :attribute є недійсним.',
    'not_regex' => 'Формат поля :attribute є недійсним.',
    'numeric' => 'Поле :attribute повинно бути числом.',
    'password' => [
        'letters' => 'Поле :attribute повинно містити принаймні одну літеру.',
        'mixed' => 'Поле :attribute повинно містити принаймні одну велику та одну малу літеру.',
        'numbers' => 'Поле :attribute повинно містити принаймні одну цифру.',
        'symbols' => 'Поле :attribute повинно містити принаймні один символ.',
        'uncompromised' => 'Дане :attribute зламали. Виберіть інше.',
    ],
    'present' => 'Поле :attribute повинно бути присутнім.',
    'present_if' => 'Поле :attribute повинно бути заповнене, коли :other має значення :value.',
    'present_unless' => 'Поле :attribute повинно бути заповнене, якщо :other не має значення :value.',
    'present_with' => 'Поле :attribute повинно бути заповнене, якщо присутнє поле :values.',
    'present_with_all' => 'Поле :attribute повинно бути заповнене, якщо заповнені поля :values.',
    'prohibited' => 'Поле :attribute заборонено.',
    'prohibited_if' => 'Поле :attribute заборонено, коли :other є :value.',
    'prohibited_unless' => 'Поле :attribute заборонено, якщо :other не є в :values.',
    'prohibits' => 'Поле :attribute забороняє присутність :other.',
    'regex' => 'Формат поля :attribute є недійсним.',
    'required' => 'Поле :attribute є обов\'язковим.',
    'required_array_keys' => 'Поле :attribute повинно містити записи для: :values.',
    'required_if' => 'Поле :attribute є обов\'язковим, коли :other є :value.',
    'required_if_accepted' => 'Поле :attribute є обов\'язковим, коли :other прийнято.',
    'required_unless' => 'Поле :attribute є обов\'язковим, якщо :other не є в :values.',
    'required_with' => 'Поле :attribute є обов\'язковим, коли :values присутні.',
    'required_with_all' => 'Поле :attribute є обов\'язковим, коли :values присутні.',
    'required_without' => 'Поле :attribute є обов\'язковим, коли :values відсутні.',
    'required_without_all' => 'Поле :attribute є обов\'язковим, коли жодне з :values не присутнє.',
    'same' => 'Поле :attribute та :other повинні збігатися.',
    'size' => [
        'array' => 'Поле :attribute повинно містити :size елементів.',
        'file' => 'Поле :attribute повинно бути :size кілобайт.',
        'numeric' => 'Поле :attribute повинно бути :size.',
        'string' => 'Поле :attribute повинно бути :size символів.',
    ],
    'starts_with' => 'Поле :attribute повинно починатися з одного з наступного: :values.',
    'string' => 'Поле :attribute повинно бути рядком.',
    'timezone' => 'Поле :attribute повинно бути дійсною зоною.',
    'unique' => 'Значення поля :attribute вже зайняте.',
    'uploaded' => 'Не вдалося завантажити поле :attribute.',
    'uppercase' => 'Поле :attribute повинно містити лише великі літери.',
    'url' => 'Формат поля :attribute є недійсним.',
    'ulid' => 'Поле :attribute має бути коректним ULID.',
    'uuid' => 'Поле :attribute повинно бути дійсним UUID.',

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
            'rule-name' => 'спеціальне повідомлення',
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
