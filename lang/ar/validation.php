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

    'accepted' => 'يجب قبول الحقل :attribute.',
    'accepted_if' => 'يجب قبول الحقل :attribute عندما يكون :other :value.',
    'active_url' => 'يجب أن يكون الحقل :attribute عنوان URL صالح.',
    'after' => 'يجب أن يكون الحقل :attribute تاريخًا بعد :date.',
    'after_or_equal' => 'يجب أن يكون الحقل :attribute تاريخًا بعد أو يساوي :date.',
    'alpha' => 'يجب أن يحتوي الحقل :attribute على أحرف فقط.',
    'alpha_dash' => 'يجب أن يحتوي الحقل :attribute على أحرف وأرقام وشرطات سفلية فقط.',
    'alpha_num' => 'يجب أن يحتوي الحقل :attribute على أحرف وأرقام فقط.',
    'array' => 'يجب أن يكون الحقل :attribute مصفوفة.',
    'ascii' => 'يجب أن يحتوي الحقل :attribute فقط على أحرف أبجدية رقمية ورموز.',
    'before' => 'يجب أن يكون الحقل :attribute تاريخ قبل :date.',
    'before_or_equal' => 'يجب أن يكون الحقل :attribute تاريخًا قبل أو يساوي :date.',
    'between' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على ما بين :min و :max عنصرًا.',
        'file' => 'يجب أن يكون الحقل :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن يكون الحقل :attribute بين :min و :max.',
        'string' => 'يجب أن يكون الحقل :attribute بين :min و :max أحرف.',
    ],
    'boolean' => 'يجب أن يكون الحقل :attribute صحيحًا أو خاطئًا.',
    'can' => 'يحتوي الحقل :attribute على قيمة غير مصرح بها.',
    'confirmed' => 'تأكيد الحقل :attribute غير متطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'يجب أن يكون الحقل :attribute تاريخًا صالحًا.',
    'date_equals' => 'يجب أن يكون الحقل :attribute تاريخًا يساوي :date.',
    'date_format' => 'يجب أن يتطابق الحقل :attribute مع تنسيق :format.',
    'decimal' => 'يجب أن يحتوي الحقل :attribute على :decimal منازل عشرية.',
    'declined' => 'يجب رفض الحقل :attribute.',
    'declined_if' => 'يجب رفض الحقل :attribute عندما يكون :other :value.',
    'different' => 'يجب أن يكون الحقل :attribute و :other مختلفين.',
    'digits' => 'يجب أن يكون الحقل :attribute :digits أرقام .',
    'digits_between' => 'يجب أن يكون الحقل :attribute بين :min و :max رقم.',
    'dimensions' => 'الحقل :attribute له أبعاد صورة غير صالحة.',
    'distinct' => 'يحتوي الحقل :attribute على قيمة مكررة.',
    'doesnt_end_with' => 'يجب ألا ينتهي الحقل :attribute بواحد مما يلي: :values.',
    'doesnt_start_with' => 'يجب ألا يبدأ حقل :attribute بواحد مما يلي: :values.',
    'email' => 'يجب أن يكون الحقل :attribute عنوان بريد إلكتروني صالح.',
    'ends_with' => 'يجب أن ينتهي الحقل :attribute بواحد مما يلي: :values.',
    'enum' => ':attribute المحدد غير صالح.',
    'exists' => ':attribute المحدد غير صالح.',
    'extensions' => 'يجب أن يحتوي الحقل :attribute على أحد الإضافات التالية: :values.',
    'file' => 'يجب أن يكون الحقل :attribute ملف.',
    'filled' => 'يجب أن يكون للحقل :attribute قيمة.',
    'gt' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على أكثر من :value عنصرًا.',
        'file' => 'يجب أن يكون الحقل :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن يكون الحقل :attribute أكبر من :value.',
        'string' => 'يجب أن يكون الحقل :attribute أكبر من :value أحرف.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على :value عنصرًا أو أكثر.',
        'file' => 'يجب أن يكون الحقل :attribute أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن يكون الحقل :attribute أكبر أو يساوي :value.',
        'string' => 'يجب أن يكون الحقل :attribute أكبر من أو يساوي :value أحرف.',
    ],
    'hex_color' => 'يجب أن يكون الحقل :attribute لون سداسي عشري صالح.',
    'image' => 'يجب أن يكون الحقل :attribute صورة.',
    'in' => ':attribute المحدد غير صالح.',
    'in_array' => 'يجب أن يكون الحقل :attribute موجودًا في :other.',
    'integer' => 'يجب أن يكون الحقل :attribute عددا صحيحًا.',
    'ip' => 'يجب أن يكون الحقل :attribute عنوان IP صالح.',
    'ipv4' => 'يجب أن يكون الحقل :attribute عنوان IPv4 صالح.',
    'ipv6' => 'يجب أن يكون الحقل :attribute عنوان IPv6 صالح.',
    'json' => 'يجب أن يكون الحقل :attribute سلسلة JSON صالحة.',
    'list' => 'يجب أن يكون الحقل :attribute قائمة.',
    'lowercase' => 'يجب أن يكون الحقل :attribute أحرف صغيرة.',
    'lt' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على أقل من :value عنصرًا.',
        'file' => 'يجب أن يكون الحقل :attribute أقل من :value كيلوبايت.',
        'numeric' => 'يجب أن يكون الحقل :attribute أقل من :value.',
        'string' => 'يجب أن يكون الحقل :attribute أقل من :value أحرف.',
    ],
    'lte' => [
        'array' => 'يجب أن لا يحتوي الحقل :attribute على أكثر من :value عنصرًا.',
        'file' => 'يجب أن يكون الحقل :attribute أقل من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن يكون الحقل :attribute أقل من أو يساوي :value.',
        'string' => 'يجب أن يكون الحقل :attribute أقل من أو يساوي :value أحرف.',
    ],
    'mac_address' => 'يجب أن يكون الحقل :attribute عنوان MAC صالح.',
    'max' => [
        'array' => 'يجب أن لا يحتوي الحقل :attribute على أكثر من :max عنصرًا.',
        'file' => 'يجب أن لا يكون الحقل :attribute أكبر من :max كيلوبايت.',
        'numeric' => 'يجب ألا يكون الحقل :attribute أكبر من :max.',
        'string' => 'يجب ألا يكون الحقل :attribute أكبر من :max أحرف.',
    ],
    'max_digits' => 'يجب ألا يحتوي الحقل :attribute على أكثر من :max رقم.',
    'mimes' => 'يجب أن يكون الحقل :attribute ملف من نوع: :values.',
    'mimetypes' => 'يجب أن يكون الحقل :attribute ملف من نوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على الأقل :min عنصرًا.',
        'file' => 'يجب أن يكون الحقل :attribute على الأقل :min كيلوبايت.',
        'numeric' => 'يجب أن يكون الحقل :attribute على الأقل :min.',
        'string' => 'يجب أن يكون الحقل :attribute على الأقل :min أحرف.',
    ],
    'min_digits' => 'يجب أن يحتوي الحقل :attribute على الأقل :min رقم.',
    'missing' => 'يجب أن يكون الحقل :attribute مفقودا.',
    'missing_if' => 'يجب أن يكون الحقل :attribute مفقودا عندما يكون :other :value.',
    'missing_unless' => 'يجب أن يكون الحقل :attribute مفقودا ما لم يكن :other :value.',
    'missing_with' => 'يجب أن يكون الحقل :attribute مفقودا عندما يكون :values موجودًا.',
    'missing_with_all' => 'يجب أن يكون الحقل :attribute مفقودا عند وجود :value.',
    'multiple_of' => 'يجب أن يكون الحقل :attribute من مضاعفات :value.',
    'not_in' => ':attribute المحدد غير صالح.',
    'not_regex' => 'صيغة الحقل :attribute غير صالحة.',
    'numeric' => 'يجب أن يكون الحقل :attribute رقم.',
    'password' => [
        'letters' => 'يجب أن يحتوي الحقل :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن يحتوي الحقل :attribute على حرف كبير واحد وحرف صغير واحد على الأقل.',
        'numbers' => 'يجب أن يحتوي الحقل :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن يحتوي الحقل :attribute على رمز خاص واحد على الأقل.',
        'uncompromised' => 'لقد ظهر :attribute المدخل في تسريب بيانات. الرجاء اختيار :attribute آخر.',
    ],
    'present' => 'يجب أن يكون الحقل :attribute موجودا.',
    'present_if' => 'يجب أن يكون الحقل :attribute موجودا عندما يكون :other :value.',
    'present_unless' => 'يجب أن يكون الحقل :attribute موجوداً ما لم يكن :other :value.',
    'present_with' => 'يجب أن يكون الحقل :attribute موجودا عند وجود :value.',
    'present_with_all' => 'يجب أن يكون الحقل :attribute موجودا عند وجود :value.',
    'prohibited' => 'الحقل :attribute محظور.',
    'prohibited_if' => 'الحقل :attribute محظور عندما يكون :other :value.',
    'prohibited_unless' => 'الحقل :attribute محظور ما لم يكن :other في :values.',
    'prohibits' => 'الحقل :attribute يمنع من تواجد :other.',
    'regex' => 'صيغة الحقل :attribute غير صالحة.',
    'required' => 'الحقل :attribute مطلوب.',
    'required_array_keys' => 'يجب أن يحتوي الحقل :attribute على إدخالات لـ:values.',
    'required_if' => 'الحقل :attribute مطلوب عندما يكون :other :value.',
    'required_if_accepted' => 'الحقل :attribute مطلوب عند قبول :other.',
    'required_unless' => 'الحقل :attribute مطلوب ما لم يكن :other في :values.',
    'required_with' => 'الحقل :attribute مطلوب عندما يكون :values موجودًا.',
    'required_with_all' => 'الحقل :attribute مطلوب عندما تكون :values موجوده.',
    'required_without' => 'الحقل :attribute مطلوب عندما لا يكون :values موجود.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا يكون أي من :values موجودة.',
    'same' => 'يجب أن يتطابق الحقل :attribute مع :other.',
    'size' => [
        'array' => 'يجب أن يحتوي الحقل :attribute على :size عنصرًا.',
        'file' => 'يجب أن يكون الحقل :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن يكون الحقل :attribute بحجم :size.',
        'string' => 'يجب أن يكون الحقل :attribute :size أحرف.',
    ],
    'starts_with' => 'يجب أن يبدأ الحقل :attribute بواحد مما يلي: :values.',
    'string' => 'يجب أن يكون الحقل :attribute سلسلة نصية.',
    'timezone' => 'يجب أن يكون الحقل :attribute منطقة زمنية صالحة.',
    'unique' => ':attribute تم أخذه بالفعل.',
    'uploaded' => 'فشل رفع :attribute.',
    'uppercase' => 'يجب أن يكون الحقل :attribute حروف كبيرة.',
    'url' => 'يجب أن يكون الحقل :attribute عنوان URL صالح.',
    'ulid' => 'يجب أن يكون الحقل :attribute ULID صالح.',
    'uuid' => 'يجب أن يكون الحقل :attribute UUID صالح.',

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
            'rule-name' => 'رسالة مخصصة',
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
