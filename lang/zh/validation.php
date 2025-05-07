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

    'accepted' => ':attribute 欄位必須被接受。',
    'accepted_if' => ':attribute 欄位在 :other 為 :value 時必須被接受。',
    'active_url' => ':attribute 欄位必須是有效的 URL。',
    'after' => ':attribute 欄位必須是在 :date 之後的日期。',
    'after_or_equal' => ':attribute 欄位必須是在 :date 之後或等於 :date 的日期。',
    'alpha' => ':attribute 欄位只能包含字母。',
    'alpha_dash' => ':attribute 欄位只能包含字母、數字、破折號和底線。',
    'alpha_num' => ':attribute 欄位只能包含字母和數字。',
    'array' => ':attribute 欄位必須是陣列。',
    'ascii' => ':attribute 欄位只能包含單位元組英數字元和符號。',
    'before' => ':attribute 欄位必須是在 :date 之前的日期。',
    'before_or_equal' => ':attribute 欄位必須是在 :date 之前或等於 :date 的日期。',
    'between' => [
        'array' => ':attribute 欄位必須有 :min 到 :max 個項目。',
        'file' => ':attribute 欄位的大小必須介於 :min 到 :max KB 之間。',
        'numeric' => ':attribute 欄位的值必須介於 :min 到 :max 之間。',
        'string' => ':attribute 欄位的長度必須介於 :min 到 :max 個字元之間。',
    ],
    'boolean' => ':attribute 欄位必須是 true 或 false。',
    'can' => ':attribute 欄位包含未授權的值。',
    'confirmed' => ':attribute 欄位的確認不符。',
    'current_password' => '目前的密碼不正確。',
    'date' => ':attribute 欄位必須是有效的日期。',
    'date_equals' => ':attribute 欄位必須是等於 :date 的日期。',
    'date_format' => ':attribute 欄位必須符合 :format 的格式。',
    'decimal' => ':attribute 欄位必須有 :decimal 位小數。',
    'declined' => ':attribute 欄位必須被拒絕。',
    'declined_if' => ':attribute 欄位在 :other 為 :value 時必須被拒絕。',
    'different' => ':attribute 欄位和 :other 必須不同。',
    'digits' => ':attribute 欄位必須是 :digits 位數字。',
    'digits_between' => ':attribute 欄位必須介於 :min 到 :max 位數字。',
    'dimensions' => ':attribute 欄位的圖片尺寸無效。',
    'distinct' => ':attribute 欄位有重複的值。',
    'doesnt_end_with' => ':attribute 欄位不能以 :values 其中之一結尾。',
    'doesnt_start_with' => ':attribute 欄位不能以 :values 其中之一開頭。',
    'email' => ':attribute 欄位必須是有效的電子郵件地址。',
    'ends_with' => ':attribute 欄位必須以 :values 其中之一結尾。',
    'enum' => '所選的 :attribute 無效。',
    'exists' => '所選的 :attribute 無效。',
    'extensions' => ':attribute 欄位必須是 :values 其中一種副檔名。',
    'file' => ':attribute 欄位必須是檔案。',
    'filled' => ':attribute 欄位必須有值。',
    'gt' => [
        'array' => ':attribute 欄位必須多於 :value 個項目。',
        'file' => ':attribute 欄位的大小必須大於 :value KB。',
        'numeric' => ':attribute 欄位的值必須大於 :value。',
        'string' => ':attribute 欄位的長度必須大於 :value 個字元。',
    ],
    'gte' => [
        'array' => ':attribute 欄位必須有 :value 個或更多項目。',
        'file' => ':attribute 欄位的大小必須大於或等於 :value KB。',
        'numeric' => ':attribute 欄位的值必須大於或等於 :value。',
        'string' => ':attribute 欄位的長度必須大於或等於 :value 個字元。',
    ],
    'hex_color' => ':attribute 欄位必須是有效的十六進位顏色。',
    'image' => ':attribute 欄位必須是圖片。',
    'in' => '所選的 :attribute 無效。',
    'in_array' => ':attribute 欄位必須存在於 :other 中。',
    'integer' => ':attribute 欄位必須是整數。',
    'ip' => ':attribute 欄位必須是有效的 IP 位址。',
    'ipv4' => ':attribute 欄位必須是有效的 IPv4 位址。',
    'ipv6' => ':attribute 欄位必須是有效的 IPv6 位址。',
    'json' => ':attribute 欄位必須是有效的 JSON 字串。',
    'list' => ':attribute 欄位必須是列表。',
    'lowercase' => ':attribute 欄位必須是小寫。',
    'lt' => [
        'array' => ':attribute 欄位必須少於 :value 個項目。',
        'file' => ':attribute 欄位的大小必須小於 :value KB。',
        'numeric' => ':attribute 欄位的值必須小於 :value。',
        'string' => ':attribute 欄位的長度必須小於 :value 個字元。',
    ],
    'lte' => [
        'array' => ':attribute 欄位不能多於 :value 個項目。',
        'file' => ':attribute 欄位的大小必須小於或等於 :value KB。',
        'numeric' => ':attribute 欄位的值必須小於或等於 :value。',
        'string' => ':attribute 欄位的長度必須小於或等於 :value 個字元。',
    ],
    'mac_address' => ':attribute 欄位必須是有效的 MAC 位址。',
    'max' => [
        'array' => ':attribute 欄位不能多於 :max 個項目。',
        'file' => ':attribute 欄位的大小不能大於 :max KB。',
        'numeric' => ':attribute 欄位的值不能大於 :max。',
        'string' => ':attribute 欄位的長度不能大於 :max 個字元。',
    ],
    'max_digits' => ':attribute 欄位不能超過 :max 位數字。',
    'mimes' => ':attribute 欄位必須是 :values 類型的檔案。',
    'mimetypes' => ':attribute 欄位必須是 :values 類型的檔案。',
    'min' => [
        'array' => ':attribute 欄位至少要有 :min 個項目。',
        'file' => ':attribute 欄位的大小至少要有 :min KB。',
        'numeric' => ':attribute 欄位的值至少要有 :min。',
        'string' => ':attribute 欄位的長度至少要有 :min 個字元。',
    ],
    'min_digits' => ':attribute 欄位至少要有 :min 位數字。',
    'missing' => ':attribute 欄位必須不存在。',
    'missing_if' => '當 :other 為 :value 時，:attribute 欄位必須不存在。',
    'missing_unless' => '除非 :other 為 :value，否則 :attribute 欄位必須不存在。',
    'missing_with' => '當 :values 存在時，:attribute 欄位必須不存在。',
    'missing_with_all' => '當所有 :values 都存在時，:attribute 欄位必須不存在。',
    'multiple_of' => ':attribute 欄位必須是 :value 的倍數。',
    'not_in' => '所選的 :attribute 無效。',
    'not_regex' => ':attribute 欄位的格式無效。',
    'numeric' => ':attribute 欄位必須是數字。',
    'password' => [
        'letters' => ':attribute 欄位必須包含至少一個字母。',
        'mixed' => ':attribute 欄位必須包含至少一個大寫字母和一個小寫字母。',
        'numbers' => ':attribute 欄位必須包含至少一個數字。',
        'symbols' => ':attribute 欄位必須包含至少一個符號。',
        'uncompromised' => '指定的 :attribute 已出現在資料外洩中。請選擇不同的 :attribute。',
    ],
    'present' => ':attribute 欄位必須存在。',
    'present_if' => '當 :other 為 :value 時，:attribute 欄位必須存在。',
    'present_unless' => '除非 :other 為 :value，否則 :attribute 欄位必須存在。',
    'present_with' => '當 :values 存在時，:attribute 欄位必須存在。',
    'present_with_all' => '當所有 :values 都存在時，:attribute 欄位必須存在。',
    'prohibited' => ':attribute 欄位被禁止。',
    'prohibited_if' => '當 :other 為 :value 時，:attribute 欄位被禁止。',
    'prohibited_unless' => '除非 :other 在 :values 中，否則 :attribute 欄位被禁止。',
    'prohibits' => ':attribute 欄位禁止 :other 出現。',
    'regex' => ':attribute 欄位的格式無效。',
    'required' => ':attribute 欄位是必填的。',
    'required_array_keys' => ':attribute 欄位必須包含 :values 的項目。',
    'required_if' => '當 :other 為 :value 時，:attribute 欄位是必填的。',
    'required_if_accepted' => '當 :other 被接受時，:attribute 欄位是必填的。',
    'required_unless' => '除非 :other 在 :values 中，否則 :attribute 欄位是必填的。',
    'required_with' => '當 :values 存在時，:attribute 欄位是必填的。',
    'required_with_all' => '當所有 :values 都存在時，:attribute 欄位是必填的。',
    'required_without' => '當 :values 不存在時，:attribute 欄位是必填的。',
    'required_without_all' => '當所有 :values 都不存在時，:attribute 欄位是必填的。',
    'same' => ':attribute 欄位必須與 :other 相符。',
    'size' => [
        'array' => ':attribute 欄位必須包含 :size 個項目。',
        'file' => ':attribute 欄位的大小必須是 :size KB。',
        'numeric' => ':attribute 欄位的值必須是 :size。',
        'string' => ':attribute 欄位的長度必須是 :size 個字元。',
    ],
    'starts_with' => ':attribute 欄位必須以 :values 其中之一開頭。',
    'string' => ':attribute 欄位必須是字串。',
    'timezone' => ':attribute 欄位必須是有效的時區。',
    'unique' => ':attribute 已經被使用。',
    'uploaded' => ':attribute 上傳失敗。',
    'uppercase' => ':attribute 欄位必須是大寫。',
    'url' => ':attribute 欄位必須是有效的 URL。',
    'ulid' => ':attribute 欄位必須是有效的 ULID。',
    'uuid' => ':attribute 欄位必須是有效的 UUID。',

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
            'rule-name' => '自訂訊息',
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
