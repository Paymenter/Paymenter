<?php

return [
    /*
    |--------------------------------------------------------------------------
    | バリデーションの言語行
    |--------------------------------------------------------------------------
    |
    | 以下の言語行には、バリデータクラスによって使用されるデフォルトのエラーメッセージが含まれています。
    | これらのルールのいくつかには複数のバージョンがあり、サイズルールのように
    | 各メッセージを調整する際に自由に変更できます。
    |
    */

    'accepted' => ':attribute は受け入れる必要があります。',
    'active_url' => ':attribute は有効なURLではありません。',
    'after' => ':attribute は :date よりも後の日付でなければなりません。',
    'after_or_equal' => ':attribute は :date 以降の日付でなければなりません。',
    'alpha' => ':attribute は文字だけを含むことができます。',
    'alpha_dash' => ':attribute は文字、数字、ダッシュ、アンダースコアのみを含むことができます。',
    'alpha_num' => ':attribute は文字と数字だけを含むことができます。',
    'array' => ':attribute は配列でなければなりません。',
    'before' => ':attribute は :date よりも前の日付でなければなりません。',
    'before_or_equal' => ':attribute は :date 以前の日付でなければなりません。',
    'between' => [
        'numeric' => ':attribute は :min から :max の間でなければなりません。',
        'file' => ':attribute は :min から :max キロバイトの間でなければなりません。',
        'string' => ':attribute は :min から :max 文字の間でなければなりません。',
        'array' => ':attribute は :min から :max 個のアイテムを持っていなければなりません。',
    ],
    'boolean' => ':attribute フィールドは true または false でなければなりません。',
    'confirmed' => ':attribute 確認が一致しません。',
    'date' => ':attribute は有効な日付ではありません。',
    'date_equals' => ':attribute は :date と等しい日付でなければなりません。',
    'date_format' => ':attribute は :format 形式と一致しません。',
    'different' => ':attribute と :other は異なる必要があります。',
    'digits' => ':attribute は :digits 桁でなければなりません。',
    'digits_between' => ':attribute は :min から :max 桁の間でなければなりません。',
    'dimensions' => ':attribute は無効な画像のサイズです。',
    'distinct' => ':attribute フィールドには重複した値があります。',
    'email' => ':attribute は有効なメールアドレスでなければなりません。',
    'ends_with' => ':attribute は以下のいずれかで終了しなければなりません :values。',
    'exists' => '選択された :attribute は無効です。',
    'file' => ':attribute はファイルでなければなりません。',
    'filled' => ':attribute フィールドは値を持っていなければなりません。',
    'gt' => [
        'numeric' => ':attribute は :value よりも大きくなければなりません。',
        'file' => ':attribute は :value キロバイトより大きくなければなりません。',
        'string' => ':attribute は :value 文字より大きくなければなりません。',
        'array' => ':attribute は :value 個以上のアイテムを持っていなければなりません。',
    ],
    'gte' => [
        'numeric' => ':attribute は :value 以上でなければなりません。',
        'file' => ':attribute は :value キロバイト以上でなければなりません。',
        'string' => ':attribute は :value 文字以上でなければなりません。',
        'array' => ':attribute は :value 個以上のアイテムを持っていなければなりません。',
    ],
    'image' => ':attribute は画像でなければなりません。',
    'in' => '選択された :attribute は無効です。',
    'in_array' => ':attribute フィールドは :other に存在しません。',
    'integer' => ':attribute は整数でなければなりません。',
    'ip' => ':attribute は有効なIPアドレスでなければなりません。',
    'ipv4' => ':attribute は有効なIPv4アドレスでなければなりません。',
    'ipv6' => ':attribute は有効なIPv6アドレスでなければなりません。',
    'json' => ':attribute は有効なJSON文字列でなければなりません。',
    'lt' => [
        'numeric' => ':attribute は :value よりも小さくなければなりません。',
        'file' => ':attribute は :value キロバイトより小さくなければなりません。',
        'string' => ':attribute は :value 文字より小さくなければなりません。',
        'array' => ':attribute は :value より少ないアイテムを持っていなければなりません。',
    ],
    'lte' => [
        'numeric' => ':attribute は :value 以下でなければなりません。',
        'file' => ':attribute は :value キロバイト以下でなければなりません。',
        'string' => ':attribute は :value 文字以下でなければなりません。',
        'array' => ':attribute は :value 個以下のアイテムを持っていなければなりません。',
    ],
    'max' => [
        'numeric' => ':attribute は :max を超えてはいけません。',
        'file' => ':attribute は :max キロバイトを超えてはいけません。',
        'string' => ':attribute は :max 文字を超えてはいけません。',
        'array' => ':attribute は :max 個以上のアイテムを持ってはいけません。',
    ],
    'mimes' => ':attribute は次のタイプのファイルでなければなりません :values。',
    'mimetypes' => ':attribute は次のタイプのファイルでなければなりません :values。',
    'min' => [
        'numeric' => ':attribute は少なくとも :min でなければなりません。',
        'file' => ':attribute は少なくとも :min キロバイトでなければなりません。',
        'string' => ':attribute は少なくとも :min 文字でなければなりません。',
        'array' => ':attribute は少なくとも :min 個のアイテムを持っていなければなりません。',
    ],
    'multiple_of' => ':attribute は :value の倍数でなければなりません。',
    'not_in' => '選択された :attribute は無効です。',
    'not_regex' => ':attribute フォーマットが無効です。',
    'numeric' => ':attribute は数字でなければなりません。',
    'password' => 'パスワードが正しくありません。',
    'present' => ':attribute フィールドが存在していなければなりません。',
    'regex' => ':attribute フォーマットが無効です。',
    'required' => ':attribute フィールドは必須です。',
    'required_if' => ':other が :value の場合、:attribute フィールドは必須です。',
    'required_unless' => ':values に存在しない限り、:attribute フィールドは必須です。',
    'required_with' => ':values が存在する場合、:attribute フィールドは必須です。',
    'required_with_all' => ':values が存在する場合、:attribute フィールドは必須です。',
    'required_without' => ':values が存在しない場合、:attribute フィールドは必須です。',
    'required_without_all' => ':values が存在しない場合、:attribute フィールドは必須です。',
    'same' => ':attribute と :other は一致しなければなりません。',
    'size' => [
        'numeric' => ':attribute は :size でなければなりません。',
        'file' => ':attribute は :size キロバイトでなければなりません。',
        'string' => ':attribute は :size 文字でなければなりません。',
        'array' => ':attribute は :size 個のアイテムを含まなければなりません。',
    ],
    'starts_with' => ':attribute は次のいずれかで始まる必要があります :values。',
    'string' => ':attribute は文字列でなければなりません。',
    'timezone' => ':attribute は有効なタイムゾーンでなければなりません。',
    'unique' => ':attribute はすでに存在します。',
    'uploaded' => ':attribute のアップロードに失敗しました。',
    'url' => ':attribute の形式が無効です。',
    'uuid' => ':attribute は有効なUUIDでなければなりません。',

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション言語行
    |--------------------------------------------------------------------------
    |
    | ここでは、属性プレースホルダーの代わりに "Eメールアドレス" のように、
    | "属性.ルール" の規則で特定のカスタム言語行を指定できます。
    | これにより、メッセージをより表現豊かにするのに役立ちます。
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性
    |--------------------------------------------------------------------------
    |
    | 次の言語行は、属性プレースホルダーをより読みやすいものに交換するために使用されます
    | 例： "email" の代わりに "Eメールアドレス" など、
    | 単にメッセージをより表現豊かにするのに役立ちます。
    |
    */

    'attributes' => [],
];
