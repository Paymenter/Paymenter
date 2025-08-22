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

    'accepted' => 'השדה :attribute צריך להיות מאושר.',
    'accepted_if' => 'יש לאשר את השדהattribute כאשר :other הוא :value.',
    'active_url' => 'השדה :attribute צריך להיות קישור תקין.',
    'after' => 'השדה :attribute צריך תאריך מאוחר יותר מ :date.',
    'after_or_equal' => ':attribute חייב להיות התאריך :date או אחריו.',
    'alpha' => 'השדה :attribute צריך להכיל רק אותיות.',
    'alpha_dash' => 'השדה :attribute צריך להכיל רק אותיות, מספרים, מקפים, ומקף תחתון.',
    'alpha_num' => 'השדה :attribute צריך להכיל רק אותיות ומספרים.',
    'array' => 'השדה :attribute צריך להיות מערך.',
    'ascii' => 'השדה :attribute צריך להכיל אך ורק תוים אלפא נומרים בגודל בייט אחד.',
    'before' => 'השדה :attribute צריך להיות תאריך מוקדם מ :date.',
    'before_or_equal' => 'השדה :attribute צריך להיות תאריך מוקדם או שווה ל :date.',
    'between' => [
        'array' => 'בשדה :attribute צריך להיות בין :min ל :max פריטים.',
        'file' => 'השדה :attribute צריך להיות בין :min ל :max קילובייט.',
        'numeric' => 'השדה :attribute חייב להיות בין :min ל-:max.',
        'string' => 'השדה :attribute צריך להיות בין :min ל :max תווים.',
    ],
    'boolean' => 'השדה :attribute צריך להיות true או false.',
    'can' => 'השדה :attribute מכיל ערך לא חוקי.',
    'confirmed' => 'האישור של השדה :attribute לא תואם.',
    'current_password' => 'סיסמא שגויה.',
    'date' => 'השדה :attribute צריך להיות תאריך תקין.',
    'date_equals' => 'השדה :attribute צריך להיות זהה ל :date.',
    'date_format' => 'השדה :attribute צריך להיות תואם לפורמט :format.',
    'decimal' => 'לשדה :attribute צריכות להיות :decimal ספרות.',
    'declined' => 'השדה :attribute לא יכול להיות מאושר.',
    'declined_if' => 'השדה :attribute לא יכול להיות מאושר כאשר :other הוא :value.',
    'different' => 'השדה :attribute והשדה :other לא יכולים להיות זהים.',
    'digits' => 'לשדה :attribute צריכות להיות :digits ספרות.',
    'digits_between' => 'השדה :attribute צריך להיות בין :min ל :max ספרות.',
    'dimensions' => 'לשדה :attribute יש ממדי תמונה לא תקינים.',
    'distinct' => 'יש לשדה :attribute ערך כפול.',
    'doesnt_end_with' => 'לשדה :field אסור להסתיים באחד מהערכים הבאים: :values.',
    'doesnt_start_with' => 'לשדה :field אסור להתחיל באחד מהערכים הבאים: :values.',
    'email' => 'השדה :attribute חייב להיות כתובת אימייל חוקית.',
    'ends_with' => 'השדה :attribute חייב להסתיים באחד מהערכים הבאים :values.',
    'enum' => 'ל :attribute שנבחר יש ערך לא חוקי.',
    'exists' => 'ל :attribute שנבחר יש ערך לא חוקי.',
    'extensions' => 'השדה :attribute צריך להכיל את אחת ההרחבות הבאות: :values.',
    'file' => 'השדה :attribute צריך להיות קובץ.',
    'filled' => 'לשדה :attribute צריך להיות ערך.',
    'gt' => [
        'array' => 'לשדה :attribute צריכים להיות יותר מ :value פריטים.',
        'file' => 'השדה :attribute צריך להיות גדול מ :value קילובייט.',
        'numeric' => 'השדה :attribute צריך להיות גדול מ :value.',
        'string' => 'השדה :attribute צריך להכיל יותר מ :value תווים.',
    ],
    'gte' => [
        'array' => 'השדה :attribute צריך להכיל :value פריטים או יותר.',
        'file' => 'השדה :attribute צריך להכיל לפחות :value קילובייט.',
        'numeric' => 'השדה :attribute צריך להיות גדול או שווה ל :value.',
        'string' => 'השדה :attribute צריך להיות באורך של לפחות :value תווים.',
    ],
    'hex_color' => 'השדה :attribute צריך להיות צבע הקסדצימלי.',
    'image' => 'השדה :attribute צריך להיות תמונה.',
    'in' => 'ה :attribute שנבחר אינו תקין.',
    'in_array' => 'השדה :attribute צריך להיות קיים ב :other.',
    'integer' => 'השדה :attribute חייב להיות מספר שלם.',
    'ip' => 'השדה :attribute צריך להיות כתובת IP תקינה.',
    'ipv4' => 'השדה :attribute צריך להיות כתובת IPv4 תקינה.',
    'ipv6' => 'השדה :attribute צריך להיות כתובת IPv6 תקינה.',
    'json' => 'השדה :attribute חייב להיות מחרוזת תקינה בפורמט JSON.',
    'list' => 'השדה :attribute חייב להיות רשימה.',
    'lowercase' => 'השדה :attribute צריך להיות באותיות קטנות.',
    'lt' => [
        'array' => 'לשדה :attribute צריכים להיות פחות מ :value פריטים.',
        'file' => 'השדה :attribute צריך להיות קטן מ :value קילובייט.',
        'numeric' => 'השדה :attribute צריך להיות פחות מ :value.',
        'string' => 'השדה :attribute צריך להיות פחות מ :value תווים.',
    ],
    'lte' => [
        'array' => 'השדה :attribute צריך להכיל לכל היותר :value פריטים.',
        'file' => 'השדה :attribute צריך להיות לכל היותר :value קילובייט.',
        'numeric' => 'השדה :attribute צריך להיות לכל היותר :value.',
        'string' => 'השדה :attribute צריך להיות לכל היותר :value תווים.',
    ],
    'mac_address' => 'השדה :attribute צריך להיות כתובת MAC תקינה.',
    'max' => [
        'array' => 'השדה :attribute צריך להכיל לכל היותר :max פריטים.',
        'file' => 'השדה :attribute צריך להיות לכל היותר :value קילובייט.',
        'numeric' => 'השדה :attribute צריך להיות לכל היותר :value.',
        'string' => 'השדה :attribute צריך להיות באורך של לכל היותר :value תווים.',
    ],
    'max_digits' => 'השדה :attribute צריך להיות לכל היותר :value ספרות.',
    'mimes' => 'השדה :atttibute צריך להיות קובץ מאחד מהסוגים: :values.',
    'mimetypes' => 'השדה :atttibute צריך להיות קובץ מאחד מהסוגים: :values.',
    'min' => [
        'array' => 'השדה :attribute צריך להכיל לכל הפחות :min פריטים.',
        'file' => 'השדה :attribute צריך להיות לכל הפחות :min קילובייט.',
        'numeric' => 'השדה :attribute צריך להיותר לכל הפחות :min.',
        'string' => 'השדה :attribute צריך להכיל לכל הפחות :min תווים.',
    ],
    'min_digits' => 'השדה :attribute צריך להיות לכל הפחות :min ספרות.',
    'missing' => 'השדה :attribute לא יכול להיות מלא.',
    'missing_if' => 'השדה :attribute לא יכול להיות מלא כאשר :other מלא.',
    'missing_unless' => 'השדה :attribure לא יכול להיות מלא בלי שהשדה :other הוא :value.',
    'missing_with' => 'השדה :attribute לא יכול להיות מלא כאשר :values קיים.',
    'missing_with_all' => 'השדה :attribute לא יכול להיות מלא כאשר :values קיימים.',
    'multiple_of' => 'השדה :attribute צריך להיות כפולה של :value.',
    'not_in' => 'ה:attribute שנבחר אינו תקין.',
    'not_regex' => 'פורמט השדה :attribute אינו תקין.',
    'numeric' => 'השדה :attribute צריך להיות מספר.',
    'password' => [
        'letters' => 'השדה :attribute צריך להכיל אות אחת לפחות.',
        'mixed' => 'השדה :attribute צריך להכיל לפחות אות קטנה אחת ואות גדולה אחת.',
        'numbers' => 'השדה :attribute צריך להכיל לפחות ספרה אחת.',
        'symbols' => 'השדה :attribute צריך להכיל לכל הפחות סימן מיוחד אחד.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present' => 'השדה :attribute חייב להיות נוכח.',
    'present_if' => 'The :attribute field must be present when :other is :value.',
    'present_unless' => 'The :attribute field must be present unless :other is :value.',
    'present_with' => 'The :attribute field must be present when :values is present.',
    'present_with_all' => 'The :attribute field must be present when :values are present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'The :attribute field format is invalid.',
    'required' => 'השדה :attribute נחוץ.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'שדה attribute: נדרש, כאשר אם :other הוא :value.',
    'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'השדה :attribute נחוץ כאשר אף אחד מבין :values קיים.',
    'same' => 'The :attribute field must match :other.',
    'size' => [
        'array' => 'The :attribute field must contain :size items.',
        'file' => 'The :attribute field must be :size kilobytes.',
        'numeric' => 'The :attribute field must be :size.',
        'string' => 'The :attribute field must be :size characters.',
    ],
    'starts_with' => 'השדה :attribute חייב להתחיל עם אחד מהבאים: :values',
    'string' => 'השדה :attribute חייב להיות מחרוזת.',
    'timezone' => 'The :attribute field must be a valid timezone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'העלאת :attribute נכשלה.',
    'uppercase' => 'The :attribute field must be uppercase.',
    'url' => 'השדה :attribute חייב להיות קישור תקין.',
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
