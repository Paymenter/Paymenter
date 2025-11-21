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

    'accepted' => ':attribute باید پذیرفته شود.',
    'accepted_if' => 'وقتی :other برابر :value است، :attribute باید پذیرفته شود.',
    'active_url' => ':attribute باید یک URL معتبر باشد.',
    'after' => ':attribute باید تاریخی پس از :date باشد.',
    'after_or_equal' => ':attribute باید تاریخی پس از یا برابر با :date باشد.',
    'alpha' => ':attribute فقط باید شامل حروف باشد.',
    'alpha_dash' => ':attribute فقط باید شامل حروف، اعداد، خط تیره و زیرخط باشد.',
    'alpha_num' => ':attribute فقط باید شامل حروف و اعداد باشد.',
    'array' => ':attribute باید آرایه باشد.',
    'ascii' => ':attribute باید فقط شامل نویسه‌ها و نمادهای تک‌بایتی لاتین باشد.',
    'before' => ':attribute باید تاریخی قبل از :date باشد.',
    'before_or_equal' => ':attribute باید تاریخی قبل از یا برابر با :date باشد.',
    'between' => [
        'array' => ':attribute باید بین :min تا :max مورد داشته باشد.',
        'file' => 'حجم :attribute باید بین :min تا :max کیلوبایت باشد.',
        'numeric' => ':attribute باید بین :min تا :max باشد.',
        'string' => 'طول :attribute باید بین :min تا :max نویسه باشد.',
    ],
    'boolean' => ':attribute باید مقدار بولی (درست یا نادرست) باشد.',
    'can' => ':attribute شامل مقدار غیرمجاز است.',
    'confirmed' => 'تأییدیه :attribute مطابقت ندارد.',
    'current_password' => 'رمز عبور نادرست است.',
    'date' => ':attribute باید یک تاریخ معتبر باشد.',
    'date_equals' => ':attribute باید تاریخی برابر با :date باشد.',
    'date_format' => ':attribute باید با قالب :format مطابقت داشته باشد.',
    'decimal' => ':attribute باید دارای :decimal رقم اعشار باشد.',
    'declined' => ':attribute باید رد شود.',
    'declined_if' => 'وقتی :other برابر :value است، :attribute باید رد شود.',
    'different' => ':attribute و :other باید متفاوت باشند.',
    'digits' => ':attribute باید :digits رقمی باشد.',
    'digits_between' => 'تعداد ارقام :attribute باید بین :min تا :max باشد.',
    'dimensions' => 'ابعاد تصویر :attribute نامعتبر است.',
    'distinct' => ':attribute دارای مقدار تکراری است.',
    'doesnt_end_with' => ':attribute نباید با یکی از مقادیر زیر پایان یابد: :values.',
    'doesnt_start_with' => ':attribute نباید با یکی از مقادیر زیر آغاز شود: :values.',
    'email' => ':attribute باید یک نشانی ایمیل معتبر باشد.',
    'ends_with' => ':attribute باید با یکی از مقادیر زیر خاتمه یابد: :values.',
    'enum' => ':attribute انتخاب‌شده نامعتبر است.',
    'exists' => ':attribute انتخاب‌شده نامعتبر است.',
    'extensions' => ':attribute باید یکی از پسوندهای زیر را داشته باشد: :values.',
    'file' => ':attribute باید فایل باشد.',
    'filled' => 'فیلد :attribute باید دارای مقدار باشد.',
    'gt' => [
        'array' => ':attribute باید بیش از :value مورد داشته باشد.',
        'file' => 'حجم :attribute باید بیش از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگ‌تر از :value باشد.',
        'string' => 'طول :attribute باید بیش از :value نویسه باشد.',
    ],
    'gte' => [
        'array' => ':attribute باید حداقل :value مورد داشته باشد.',
        'file' => 'حجم :attribute باید بزرگ‌تر یا برابر :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگ‌تر یا برابر :value باشد.',
        'string' => 'طول :attribute باید بزرگ‌تر یا برابر :value نویسه باشد.',
    ],
    'hex_color' => ':attribute باید یک رنگ شانزده‌هشتی معتبر باشد.',
    'image' => ':attribute باید تصویر باشد.',
    'in' => ':attribute انتخاب‌شده نامعتبر است.',
    'in_array' => 'فیلد :attribute باید در :other موجود باشد.',
    'integer' => ':attribute باید عدد صحیح باشد.',
    'ip' => ':attribute باید یک نشانی IP معتبر باشد.',
    'ipv4' => ':attribute باید یک نشانی IPv4 معتبر باشد.',
    'ipv6' => ':attribute باید یک نشانی IPv6 معتبر باشد.',
    'json' => ':attribute باید یک رشته JSON معتبر باشد.',
    'list' => ':attribute باید یک فهرست باشد.',
    'lowercase' => ':attribute باید با حروف کوچک باشد.',
    'lt' => [
        'array' => ':attribute باید کمتر از :value مورد داشته باشد.',
        'file' => 'حجم :attribute باید کمتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کمتر از :value باشد.',
        'string' => 'طول :attribute باید کمتر از :value نویسه باشد.',
    ],
    'lte' => [
        'array' => ':attribute نباید بیش از :value مورد داشته باشد.',
        'file' => 'حجم :attribute باید کمتر یا برابر :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کمتر یا برابر :value باشد.',
        'string' => 'طول :attribute باید کمتر یا برابر :value نویسه باشد.',
    ],
    'mac_address' => ':attribute باید یک نشانی MAC معتبر باشد.',
    'max' => [
        'array' => ':attribute نباید بیش از :max مورد داشته باشد.',
        'file' => 'حجم :attribute نباید بیش از :max کیلوبایت باشد.',
        'numeric' => ':attribute نباید بیش از :max باشد.',
        'string' => 'طول :attribute نباید بیش از :max نویسه باشد.',
    ],
    'max_digits' => ':attribute نباید بیش از :max رقم داشته باشد.',
    'mimes' => ':attribute باید فایلی از نوع‌های: :values باشد.',
    'mimetypes' => ':attribute باید فایلی از نوع‌های: :values باشد.',
    'min' => [
        'array' => ':attribute باید حداقل :min مورد داشته باشد.',
        'file' => 'حجم :attribute باید حداقل :min کیلوبایت باشد.',
        'numeric' => ':attribute باید حداقل :min باشد.',
        'string' => 'طول :attribute باید حداقل :min نویسه باشد.',
    ],
    'min_digits' => ':attribute باید حداقل :min رقم داشته باشد.',
    'missing' => 'فیلد :attribute باید خالی باشد.',
    'missing_if' => 'وقتی :other برابر :value است، فیلد :attribute باید خالی باشد.',
    'missing_unless' => 'فیلد :attribute باید خالی باشد مگر اینکه :other برابر :value باشد.',
    'missing_with' => 'وقتی :values موجود است، فیلد :attribute باید خالی باشد.',
    'missing_with_all' => 'وقتی :values موجود هستند، فیلد :attribute باید خالی باشد.',
    'multiple_of' => ':attribute باید مضربی از :value باشد.',
    'not_in' => ':attribute انتخاب‌شده نامعتبر است.',
    'not_regex' => 'قالب :attribute نامعتبر است.',
    'numeric' => ':attribute باید عدد باشد.',
    'password' => [
        'letters' => ':attribute باید حداقل یک حرف داشته باشد.',
        'mixed' => ':attribute باید حداقل یک حرف بزرگ و یک حرف کوچک داشته باشد.',
        'numbers' => ':attribute باید حداقل یک عدد داشته باشد.',
        'symbols' => ':attribute باید حداقل یک نماد داشته باشد.',
        'uncompromised' => ':attribute ارائه‌شده در یک نشت داده مشاهده شده است. لطفاً :attribute دیگری انتخاب کنید.',
    ],
    'present' => 'فیلد :attribute باید موجود باشد.',
    'present_if' => 'وقتی :other برابر :value است، فیلد :attribute باید موجود باشد.',
    'present_unless' => 'فیلد :attribute باید موجود باشد مگر اینکه :other برابر :value باشد.',
    'present_with' => 'وقتی :values موجود است، فیلد :attribute باید موجود باشد.',
    'present_with_all' => 'وقتی :values موجود هستند، فیلد :attribute باید موجود باشد.',
    'prohibited' => 'فیلد :attribute ممنوع است.',
    'prohibited_if' => 'وقتی :other برابر :value است، فیلد :attribute ممنوع است.',
    'prohibited_unless' => 'فیلد :attribute ممنوع است مگر اینکه :other در :values باشد.',
    'prohibits' => 'فیلد :attribute مانع از حضور :other می‌شود.',
    'regex' => 'قالب :attribute نامعتبر است.',
    'required' => 'فیلد :attribute الزامی است.',
    'required_array_keys' => 'فیلد :attribute باید شامل ورودی‌هایی برای این موارد باشد: :values.',
    'required_if' => 'وقتی :other برابر :value است، فیلد :attribute الزامی است.',
    'required_if_accepted' => 'وقتی :other پذیرفته شده است، فیلد :attribute الزامی است.',
    'required_unless' => 'فیلد :attribute الزامی است مگر اینکه :other در :values باشد.',
    'required_with' => 'وقتی :values موجود است، فیلد :attribute الزامی است.',
    'required_with_all' => 'وقتی :values موجود هستند، فیلد :attribute الزامی است.',
    'required_without' => 'وقتی :values موجود نیست، فیلد :attribute الزامی است.',
    'required_without_all' => 'وقتی هیچ‌کدام از :values موجود نیستند، فیلد :attribute الزامی است.',
    'same' => ':attribute باید با :other مطابقت داشته باشد.',
    'size' => [
        'array' => ':attribute باید شامل :size مورد باشد.',
        'file' => 'حجم :attribute باید :size کیلوبایت باشد.',
        'numeric' => ':attribute باید :size باشد.',
        'string' => 'طول :attribute باید :size نویسه باشد.',
    ],
    'starts_with' => ':attribute باید با یکی از مقادیر زیر آغاز شود: :values.',
    'string' => ':attribute باید رشته باشد.',
    'timezone' => ':attribute باید یک منطقه زمانی معتبر باشد.',
    'unique' => ':attribute قبلاً گرفته شده است.',
    'uploaded' => 'بارگذاری :attribute انجام نشد.',
    'uppercase' => ':attribute باید با حروف بزرگ باشد.',
    'url' => ':attribute باید یک URL معتبر باشد.',
    'ulid' => ':attribute باید یک ULID معتبر باشد.',
    'uuid' => ':attribute باید یک UUID معتبر باشد.',

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

    'attributes' => [
        'email' => 'ایمیل',
        'password' => 'رمز عبور',
        'name' => 'نام',
        'first_name' => 'نام',
        'last_name' => 'نام خانوادگی',
        'username' => 'نام کاربری',
        'phone' => 'تلفن',
        'city' => 'شهر',
        'country' => 'کشور',
        'address' => 'آدرس',
        'zip' => 'کد پستی',
        'state' => 'استان',
        'title' => 'عنوان',
        'body' => 'متن',
        'description' => 'توضیحات',
        'subject' => 'موضوع',
        'message' => 'پیام',
        'file' => 'فایل',
        'image' => 'تصویر',
        'amount' => 'مبلغ',
    ],

];
