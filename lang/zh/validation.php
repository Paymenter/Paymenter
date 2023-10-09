<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 驗證語言行
    |--------------------------------------------------------------------------
    |
    | 以下語言行包含了驗證器類別使用的預設錯誤訊息。其中一些規則有多個版本，例如大小規則。請隨意在這裡微調每個訊息。
    |
    */

    'accepted' => ':attribute 必須被接受。',
    'active_url' => ':attribute 不是有效的URL。',
    'after' => ':attribute 必須是 :date 之後的日期。',
    'after_or_equal' => ':attribute 必須是 :date 之後或相等的日期。',
    'alpha' => ':attribute 只能包含字母。',
    'alpha_dash' => ':attribute 只能包含字母、數字、破折號和底線。',
    'alpha_num' => ':attribute 只能包含字母和數字。',
    'array' => ':attribute 必須是一個陣列。',
    'before' => ':attribute 必須是 :date 之前的日期。',
    'before_or_equal' => ':attribute 必須是 :date 之前或相等的日期。',
    'between' => [
        'numeric' => ':attribute 必須在 :min 到 :max 之間。',
        'file' => ':attribute 必須在 :min 到 :max 單位之間。',
        'string' => ':attribute 必須在 :min 到 :max 個字元之間。',
        'array' => ':attribute 必須有 :min 到 :max 個項目。',
    ],
    'boolean' => ':attribute 欄位必須為 true 或 false。',
    'confirmed' => ':attribute 確認不符。',
    'date' => ':attribute 不是有效的日期。',
    'date_equals' => ':attribute 必須是等於 :date 的日期。',
    'date_format' => ':attribute 不符合格式 :format。',
    'different' => ':attribute 和 :other 必須不同。',
    'digits' => ':attribute 必須為 :digits 位數字。',
    'digits_between' => ':attribute 必須在 :min 到 :max 位數字之間。',
    'dimensions' => ':attribute 圖片尺寸無效。',
    'distinct' => ':attribute 欄位具有重複值。',
    'email' => ':attribute 必須是有效的電子郵件地址。',
    'ends_with' => ':attribute 必須以以下之一結尾: :values。',
    'exists' => '所選擇的 :attribute 無效。',
    'file' => ':attribute 必須是一個文件。',
    'filled' => ':attribute 欄位必須有值。',
    'gt' => [
        'numeric' => ':attribute 必須大於 :value。',
        'file' => ':attribute 必須大於 :value 單位。',
        'string' => ':attribute 必須大於 :value 個字元。',
        'array' => ':attribute 必須有多於 :value 個項目。',
    ],
    'gte' => [
        'numeric' => ':attribute 必須大於等於 :value。',
        'file' => ':attribute 必須大於等於 :value 單位。',
        'string' => ':attribute 必須大於等於 :value 個字元。',
        'array' => ':attribute 必須有 :value 個項目或以上。',
    ],
    'image' => ':attribute 必須是一個圖片。',
    'in' => '所選擇的 :attribute 無效。',
    'in_array' => ':attribute 欄位在 :other 中不存在。',
    'integer' => ':attribute 必須是整數。',
    'ip' => ':attribute 必須是有效的IP地址。',
    'ipv4' => ':attribute 必須是有效的IPv4地址。',
    'ipv6' => ':attribute 必須是有效的IPv6地址。',
    'json' => ':attribute 必須是有效的JSON字串。',
    'lt' => [
        'numeric' => ':attribute 必須小於 :value。',
        'file' => ':attribute 必須小於 :value 單位。',
        'string' => ':attribute 必須小於 :value 個字元。',
        'array' => ':attribute 必須有少於 :value 個項目。',
    ],
    'lte' => [
        'numeric' => ':attribute 必須小於等於 :value。',
        'file' => ':attribute 必須小於等於 :value 單位。',
        'string' => ':attribute 必須小於等於 :value 個字元。',
        'array' => ':attribute 不得有多於 :value 個項目。',
    ],
    'max' => [
        'numeric' => ':attribute 可能不大於 :max。',
        'file' => ':attribute 可能不大於 :max 單位。',
        'string' => ':attribute 可能不大於 :max 個字元。',
        'array' => ':attribute 可能不超過 :max 個項目。',
    ],
    'mimes' => ':attribute 必須是類型為 :values 的文件。',
    'mimetypes' => ':attribute 必須是類型為 :values 的文件。',
    'min' => [
        'numeric' => ':attribute 必須至少為 :min。',
        'file' => ':attribute 必須至少為 :min 單位。',
        'string' => ':attribute 必須至少為 :min 個字元。',
        'array' => ':attribute 必須至少有 :min 個項目。',
    ],
    'multiple_of' => ':attribute 必須是 :value 的倍數。',
    'not_in' => '所選擇的 :attribute 無效。',
    'not_regex' => ':attribute 格式無效。',
    'numeric' => ':attribute 必須是數字。',
    'password' => '密碼不正確。',
    'present' => ':attribute 欄位必須存在。',
    'regex' => ':attribute 格式無效。',
    'required' => ':attribute 欄位是必需的。',
    'required_if' => '當 :other 是 :value 時，:attribute 欄位是必需的。',
    'required_unless' => '除非 :other 在 :values 中，否則 :attribute 欄位是必需的。',
    'required_with' => '當存在 :values 時，:attribute 欄位是必需的。',
    'required_with_all' => '當存在所有 :values 時，:attribute 欄位是必需的。',
    'required_without' => '當不存在 :values 時，:attribute 欄位是必需的。',
    'required_without_all' => '當所有 :values 皆不存在時，:attribute 欄位是必需的。',
    'same' => ':attribute 和 :other 必須匹配。',
    'size' => [
        'numeric' => ':attribute 必須是 :size。',
        'file' => ':attribute 必須是 :size 單位。',
        'string' => ':attribute 必須是 :size 個字元。',
        'array' => ':attribute 必須包含 :size 個項目。',
    ],
    'starts_with' => ':attribute 必須以以下之一開始: :values。',
    'string' => ':attribute 必須是一個字串。',
    'timezone' => ':attribute 必須是一個有效的時區。',
    'unique' => ':attribute 已經被使用。',
    'uploaded' => ':attribute 上傳失敗。',
    'url' => ':attribute 格式無效。',
    'uuid' => ':attribute 必須是一個有效的UUID。',

    /*
    |--------------------------------------------------------------------------
    | 自定義驗證語言行
    |--------------------------------------------------------------------------
    |
    | 在這裡，您可以為屬性指定自定義驗證消息，使用“attribute.rule”命名行。這使我們能夠快速為給定屬性規則指定特定的自定義語言行。
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 自定義驗證屬性
    |--------------------------------------------------------------------------
    |
    | 以下語言行用於將我們的屬性占位符與更具表達力的內容交換，例如“電子郵件地址”而不是“email”。這只是幫助我們使我們的消息更具表達性的簡單方式。
    |
    */

    'attributes' => [],
];
