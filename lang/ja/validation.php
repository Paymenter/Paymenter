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

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeには有効なURLを指定してください。',
    'after' => ':attributeには:dateより後の日付を指定してください。',
    'after_or_equal' => ':attributeには:date以降の日付を指定してください。',
    'alpha' => ':attributeには文字のみ使用できます（数字や記号は使用できません）。',
    'alpha_dash' => ':attributeには文字、数字、ハイフン（-）、アンダースコア（_）のみ使用できます。',
    'alpha_num' => ':attributeには文字と数字のみ使用できます。',
    'array' => ':attributeは配列にしてください。',
    'ascii' => ':attributeには半角英数字と半角記号のみ使用できます。',
    'before' => ':attributeには:dateより前の日付を指定してください。',
    'before_or_equal' => ':attributeには:date以前の日付を指定してください。',
    'between' => [
        'array' => ':attributeの項目数は:min個から:max個の間にしてください。',
        'file' => ':attributeは:minキロバイトから:maxキロバイトの間にしてください。',
        'numeric' => ':attributeは:minから:maxの間で指定してください。',
        'string' => ':attributeは:min文字から:max文字の間で指定してください。',
    ],
    'boolean' => ':attributeはtrueまたはfalseにしてください。',
    'can' => ':attributeに許可されていない値が含まれています。',
    'confirmed' => ':attributeが確認用の値と一致しません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attributeには有効な日付を指定してください。',
    'date_equals' => ':attributeには:dateと同じ日付を指定してください。',
    'date_format' => ':attributeは:format形式で指定してください。',
    'decimal' => ':attributeは小数点以下:decimal桁で指定してください。',
    'declined' => ':attributeは拒否してください。',
    'declined_if' => ':otherが:valueの場合、:attributeは拒否してください。',
    'different' => ':attributeと:otherには異なる値を指定してください。',
    'digits' => ':attributeは:digits桁で指定してください。',
    'digits_between' => ':attributeは:min桁から:max桁の間で指定してください。',
    'dimensions' => ':attributeの画像サイズが無効です。',
    'distinct' => ':attributeに重複した値があります。',
    'doesnt_end_with' => ':attributeの末尾を次のいずれかにすることはできません: :values。',
    'doesnt_start_with' => ':attributeの先頭を次のいずれかにすることはできません: :values。',
    'email' => ':attributeには有効なメールアドレスを指定してください。',
    'ends_with' => ':attributeの末尾は次のいずれかにしてください: :values。',
    'enum' => '選択された:attributeは無効です。',
    'exists' => '選択された:attributeは無効です。',
    'extensions' => ':attributeは次のいずれかの拡張子のファイルにしてください: :values。',
    'file' => ':attributeにはファイルを指定してください。',
    'filled' => ':attributeには値を入力してください。',
    'gt' => [
        'array' => ':attributeには:value個より多くの項目を指定してください。',
        'file' => ':attributeは:valueキロバイトより大きくしてください。',
        'numeric' => ':attributeは:valueより大きい値にしてください。',
        'string' => ':attributeは:value文字より多くしてください。',
    ],
    'gte' => [
        'array' => ':attributeには:value個以上の項目を指定してください。',
        'file' => ':attributeは:valueキロバイト以上にしてください。',
        'numeric' => ':attributeは:value以上の値にしてください。',
        'string' => ':attributeは:value文字以上にしてください。',
    ],
    'hex_color' => ':attributeには有効な16進数カラーコードを指定してください。',
    'image' => ':attributeには画像を指定してください。',
    'in' => '選択された:attributeは無効です。',
    'in_array' => ':attributeは:otherに含まれる値にしてください。',
    'integer' => ':attributeは整数にしてください。',
    'ip' => ':attributeには有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeには有効なIPv4アドレスを指定してください。',
    'ipv6' => ':attributeには有効なIPv6アドレスを指定してください。',
    'json' => ':attributeには有効なJSON文字列を指定してください。',
    'list' => ':attributeはリストにしてください。',
    'lowercase' => ':attributeは小文字で入力してください。',
    'lt' => [
        'array' => ':attributeには:value個未満の項目を指定してください。',
        'file' => ':attributeは:valueキロバイト未満にしてください。',
        'numeric' => ':attributeは:value未満の値にしてください。',
        'string' => ':attributeは:value文字未満にしてください。',
    ],
    'lte' => [
        'array' => ':attributeの項目数は:value個以下にしてください。',
        'file' => ':attributeは:valueキロバイト以下にしてください。',
        'numeric' => ':attributeは:value以下の値にしてください。',
        'string' => ':attributeは:value文字以下にしてください。',
    ],
    'mac_address' => ':attributeには有効なMACアドレスを指定してください。',
    'max' => [
        'array' => ':attributeの項目数は:max個以下にしてください。',
        'file' => ':attributeは:maxキロバイト以下にしてください。',
        'numeric' => ':attributeは:max以下の値にしてください。',
        'string' => ':attributeは:max文字以下にしてください。',
    ],
    'max_digits' => ':attributeは:max桁以下にしてください。',
    'mimes' => ':attributeは次の形式のファイルにしてください: :values。',
    'mimetypes' => ':attributeは次の形式のファイルにしてください: :values。',
    'min' => [
        'array' => ':attributeには:min個以上の項目を指定してください。',
        'file' => ':attributeは:minキロバイト以上にしてください。',
        'numeric' => ':attributeは:min以上の値にしてください。',
        'string' => ':attributeは:min文字以上にしてください。',
    ],
    'min_digits' => ':attributeは:min桁以上にしてください。',
    'missing' => ':attributeは指定しないでください。',
    'missing_if' => ':otherが:valueの場合、:attributeは指定しないでください。',
    'missing_unless' => ':otherが:valueでない場合、:attributeは指定しないでください。',
    'missing_with' => ':valuesが指定されている場合、:attributeは指定しないでください。',
    'missing_with_all' => ':valuesがすべて指定されている場合、:attributeは指定しないでください。',
    'multiple_of' => ':attributeは:valueの倍数にしてください。',
    'not_in' => '選択された:attributeは無効です。',
    'not_regex' => ':attributeの形式が正しくありません。',
    'numeric' => ':attributeは数値にしてください。',
    'password' => [
        'letters' => ':attributeには数字・記号以外の文字を1文字以上含めてください。',
        'mixed' => ':attributeには大文字と小文字をそれぞれ1文字以上含めてください。',
        'numbers' => ':attributeには数字を1文字以上含めてください。',
        'symbols' => ':attributeには記号を1文字以上含めてください。',
        'uncompromised' => '入力された:attributeは過去のデータ漏洩で流出したことがあります。別の:attributeを指定してください。',
    ],
    'present' => ':attributeの項目が存在している必要があります。',
    'present_if' => ':otherが:valueの場合、:attributeの項目が存在している必要があります。',
    'present_unless' => ':otherが:valueでない場合、:attributeの項目が存在している必要があります。',
    'present_with' => ':valuesが指定されている場合、:attributeの項目が存在している必要があります。',
    'present_with_all' => ':valuesがすべて指定されている場合、:attributeの項目が存在している必要があります。',
    'prohibited' => ':attributeは入力できません。',
    'prohibited_if' => ':otherが:valueの場合、:attributeは入力できません。',
    'prohibited_unless' => ':otherが:valuesのいずれかでない場合、:attributeは入力できません。',
    'prohibits' => ':attributeが入力されている場合、:otherは入力できません。',
    'regex' => ':attributeの形式が正しくありません。',
    'required' => ':attributeは必須です。',
    'required_array_keys' => ':attributeには次の項目を含めてください: :values。',
    'required_if' => ':otherが:valueの場合、:attributeは必須です。',
    'required_if_accepted' => ':otherが承認されている場合、:attributeは必須です。',
    'required_unless' => ':otherが:valuesのいずれかでない場合、:attributeは必須です。',
    'required_with' => ':valuesが指定されている場合、:attributeは必須です。',
    'required_with_all' => ':valuesがすべて指定されている場合、:attributeは必須です。',
    'required_without' => ':valuesが指定されていない場合、:attributeは必須です。',
    'required_without_all' => ':valuesがいずれも指定されていない場合、:attributeは必須です。',
    'same' => ':attributeは:otherと一致させてください。',
    'size' => [
        'array' => ':attributeの項目数は:size個にしてください。',
        'file' => ':attributeは:sizeキロバイトにしてください。',
        'numeric' => ':attributeは:sizeにしてください。',
        'string' => ':attributeは:size文字にしてください。',
    ],
    'starts_with' => ':attributeの先頭は次のいずれかにしてください: :values。',
    'string' => ':attributeは文字列にしてください。',
    'timezone' => ':attributeには有効なタイムゾーンを指定してください。',
    'unique' => ':attributeはすでに使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeは大文字で入力してください。',
    'url' => ':attributeには有効なURLを指定してください。',
    'ulid' => ':attributeには有効なULIDを指定してください。',
    'uuid' => ':attributeには有効なUUIDを指定してください。',

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
