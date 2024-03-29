<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Языковые Ресурсы Валидации
    |--------------------------------------------------------------------------
    |
    | Следующие языковые строки содержат стандартные сообщения об ошибках, используемые
    | классом валидатора. Некоторые из этих правил имеют несколько версий, таких
    | как правила размера. Не стесняйтесь настраивать каждое из этих сообщений здесь.
    |
    */

    'accepted' => 'Поле :attribute должно быть принято.',
    'active_url' => 'Поле :attribute не является допустимым URL.',
    'after' => 'Поле :attribute должно быть датой после :date.',
    'after_or_equal' => 'Поле :attribute должно быть датой после или равной :date.',
    'alpha' => 'Поле :attribute может содержать только буквы.',
    'alpha_dash' => 'Поле :attribute может содержать только буквы, цифры, дефисы и подчеркивания.',
    'alpha_num' => 'Поле :attribute может содержать только буквы и цифры.',
    'array' => 'Поле :attribute должно быть массивом.',
    'before' => 'Поле :attribute должно быть датой до :date.',
    'before_or_equal' => 'Поле :attribute должно быть датой до или равной :date.',
    'between' => [
        'numeric' => 'Поле :attribute должно быть между :min и :max.',
        'file' => 'Поле :attribute должно быть между :min и :max килобайтами.',
        'string' => 'Поле :attribute должно быть между :min и :max символами.',
        'array' => 'Поле :attribute должно иметь от :min до :max элементов.',
    ],
    'boolean' => 'Поле :attribute должно быть истинным или ложным.',
    'confirmed' => 'Подтверждение поля :attribute не совпадает.',
    'date' => 'Поле :attribute не является допустимой датой.',
    'date_equals' => 'Поле :attribute должно быть датой, равной :date.',
    'date_format' => 'Поле :attribute не соответствует формату :format.',
    'different' => 'Поля :attribute и :other должны быть различными.',
    'digits' => 'Поле :attribute должно быть :digits цифрами.',
    'digits_between' => 'Поле :attribute должно быть между :min и :max цифрами.',
    'dimensions' => 'Поле :attribute имеет недопустимые размеры изображения.',
    'distinct' => 'Поле :attribute имеет повторяющееся значение.',
    'email' => 'Поле :attribute должно быть действительным адресом электронной почты.',
    'ends_with' => 'Поле :attribute должно заканчиваться одним из следующих значений: :values.',
    'exists' => 'Выбранное значение для :attribute недопустимо.',
    'file' => 'Поле :attribute должно быть файлом.',
    'filled' => 'Поле :attribute обязательно для заполнения.',
    'gt' => [
        'numeric' => 'Поле :attribute должно быть больше :value.',
        'file' => 'Поле :attribute должно быть больше :value килобайт.',
        'string' => 'Поле :attribute должно быть больше :value символов.',
        'array' => 'Поле :attribute должно иметь больше :value элементов.',
    ],
    'gte' => [
        'numeric' => 'Поле :attribute должно быть больше или равно :value.',
        'file' => 'Поле :attribute должно быть больше или равно :value килобайт.',
        'string' => 'Поле :attribute должно быть больше или равно :value символов.',
        'array' => 'Поле :attribute должно иметь :value элементов или больше.',
    ],
    'image' => 'Поле :attribute должно быть изображением.',
    'in' => 'Выбранное значение для :attribute ошибочно.',
    'in_array' => 'Поле :attribute не существует в :other.',
    'integer' => 'Поле :attribute должно быть целым числом.',
    'ip' => 'Поле :attribute должно быть действительным IP-адресом.',
    'ipv4' => 'Поле :attribute должно быть действительным IPv4-адресом.',
    'ipv6' => 'Поле :attribute должно быть действительным IPv6-адресом.',
    'json' => 'Поле :attribute должно быть действительной JSON строкой.',
    'lt' => [
        'numeric' => 'Поле :attribute должно быть меньше :value.',
        'file' => 'Поле :attribute должно быть меньше :value килобайт.',
        'string' => 'Поле :attribute должно быть меньше :value символов.',
        'array' => 'Поле :attribute должно иметь меньше :value элементов.',
    ],
    'lte' => [
        'numeric' => 'Поле :attribute должно быть меньше или равно :value.',
        'file' => 'Поле :attribute должно быть меньше или равно :value килобайт.',
        'string' => 'Поле :attribute должно быть меньше или равно :value символов.',
        'array' => 'Поле :attribute не должно иметь больше :value элементов.',
    ],
    'max' => [
        'numeric' => 'Поле :attribute не может быть более :max.',
        'file' => 'Поле :attribute не может быть более :max килобайт.',
        'string' => 'Поле :attribute не может быть более :max символов.',
        'array' => 'Поле :attribute не может иметь более :max элементов.',
    ],
    'mimes' => 'Поле :attribute должно быть файлом типа: :values.',
    'mimetypes' => 'Поле :attribute должно быть файлом типа: :values.',
    'min' => [
        'numeric' => 'Поле :attribute должно быть не менее :min.',
        'file' => 'Поле :attribute должно быть не менее :min килобайт.',
        'string' => 'Поле :attribute должно быть не менее :min символов.',
        'array' => 'Поле :attribute должно иметь не менее :min элементов.',
    ],
    'multiple_of' => 'Поле :attribute должно быть кратным :value.',
    'not_in' => 'Выбранное значение для :attribute ошибочно.',
    'not_regex' => 'Формат поля :attribute недопустим.',
    'numeric' => 'Поле :attribute должно быть числом.',
    'password' => 'Неверный пароль.',
    'present' => 'Поле :attribute должно присутствовать.',
    'regex' => 'Формат поля :attribute недопустим.',
    'required' => 'Поле :attribute обязательно для заполнения.',
    'required_if' => 'Поле :attribute обязательно для заполнения, когда :other равно :value.',
    'required_unless' => 'Поле :attribute обязательно для заполнения, если :other не находится в :values.',
    'required_with' => 'Поле :attribute обязательно для заполнения, когда :values присутствует.',
    'required_with_all' => 'Поле :attribute обязательно для заполнения, когда :values присутствуют.',
    'required_without' => 'Поле :attribute обязательно для заполнения, когда :values отсутствует.',
    'required_without_all' => 'Поле :attribute обязательно для заполнения, когда ни одно из :values не присутствует.',
    'same' => 'Поле :attribute и :other должны совпадать.',
    'size' => [
        'numeric' => 'Поле :attribute должно быть равно :size.',
        'file' => 'Поле :attribute должно быть равно :size килобайт.',
        'string' => 'Поле :attribute должно быть равно :size символов.',
        'array' => 'Поле :attribute должно содержать :size элементов.',
    ],
    'starts_with' => 'Поле :attribute должно начинаться с одного из следующих значений: :values.',
    'string' => 'Поле :attribute должно быть строкой.',
    'timezone' => 'Поле :attribute должно быть действительным часовым поясом.',
    'unique' => 'Такое значение поля :attribute уже существует.',
    'uploaded' => 'Загрузка поля :attribute не удалась.',
    'url' => 'Формат поля :attribute недопустим.',
    'uuid' => 'Поле :attribute должно быть действительным UUID.',

    /*
    |--------------------------------------------------------------------------
    | Специальные Языковые Ресурсы Валидации
    |--------------------------------------------------------------------------
    |
    | Здесь вы можете указать собственные сообщения для атрибутов, используя
    | конвенцию "attribute.rule" для именования строк. Это быстро позволяет
    | указать конкретное пользовательское сообщение для данного правила атрибута.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'сообщение-пользователя',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Специальные Названия Атрибутов Валидации
    |--------------------------------------------------------------------------
    |
    | Следующие языковые строки используются для замены заполнителей атрибутов
    | на что-то более читаемое, например "Адрес Электронной Почты" вместо
    | "email". Это помогает сделать сообщение более понятным.
    |
    */

    'attributes' => [],
];
