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

    'accepted' => ':attribute फ़ील्ड को स्वीकार किया जाना चाहिए।',
    'accepted_if' => ':attribute फ़ील्ड को स्वीकार किया जाना चाहिए जब :other :value हो।',
    'active_url' => ':attribute फ़ील्ड एक वैध URL होना चाहिए।',
    'after' => ':attribute फ़ील्ड को :date के बाद की तिथि होनी चाहिए।',
    'after_or_equal' => ':attribute फ़ील्ड को :date के बाद या बराबर की तिथि होनी चाहिए।',
    'alpha' => ':attribute फ़ील्ड में केवल अक्षर होने चाहिए।',
    'alpha_dash' => ':attribute फ़ील्ड में केवल अक्षर, संख्याएँ, डैश और अंडरस्कोर होने चाहिए।',
    'alpha_num' => ':attribute फ़ील्ड में केवल अक्षर और संख्याएँ होनी चाहिए।',
    'array' => ':attribute फ़ील्ड को एक ऐरे होना चाहिए।',
    'ascii' => ':attribute फ़ील्ड में केवल एकल-बाइट अल्फ़ान्यूमेरिक कैरेक्टर और प्रतीक होने चाहिए।',
    'before' => ':attribute फ़ील्ड को :date से पहले की तिथि होनी चाहिए।',
    'before_or_equal' => ':attribute फ़ील्ड को :date से पहले या बराबर की तिथि होनी चाहिए।',
    'between' => [
        'array' => ':attribute फ़ील्ड में :min और :max आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड का आकार :min और :max किलोबाइट्स के बीच होना चाहिए।',
        'numeric' => ':attribute फ़ील्ड :min और :max के बीच होना चाहिए।',
        'string' => ':attribute फ़ील्ड में :min और :max कैरेक्टर होने चाहिए।',
    ],
    'boolean' => ':attribute फ़ील्ड को सही या गलत होना चाहिए।',
    'can' => ':attribute फ़ील्ड में एक अवैध मान है।',
    'confirmed' => ':attribute फ़ील्ड की पुष्टि मेल नहीं खाती।',
    'current_password' => 'पासवर्ड गलत है।',
    'date' => ':attribute फ़ील्ड एक वैध तिथि होनी चाहिए।',
    'date_equals' => ':attribute फ़ील्ड को :date के बराबर की तिथि होनी चाहिए।',
    'date_format' => ':attribute फ़ील्ड को :format प्रारूप से मेल खाना चाहिए।',
    'decimal' => ':attribute फ़ील्ड में :decimal दशमलव स्थान होने चाहिए।',
    'declined' => ':attribute फ़ील्ड को अस्वीकार किया जाना चाहिए।',
    'declined_if' => ':attribute फ़ील्ड को अस्वीकार किया जाना चाहिए जब :other :value हो।',
    'different' => ':attribute फ़ील्ड और :other अलग होने चाहिए।',
    'digits' => ':attribute फ़ील्ड में :digits अंक होने चाहिए।',
    'digits_between' => ':attribute फ़ील्ड में :min और :max अंक होने चाहिए।',
    'dimensions' => ':attribute फ़ील्ड का चित्र आयाम अवैध है।',
    'distinct' => ':attribute फ़ील्ड में एक डुप्लिकेट मान है।',
    'doesnt_end_with' => ':attribute फ़ील्ड को निम्नलिखित में से किसी के साथ समाप्त नहीं होना चाहिए: :values।',
    'doesnt_start_with' => ':attribute फ़ील्ड को निम्नलिखित में से किसी के साथ शुरू नहीं होना चाहिए: :values।',
    'email' => ':attribute फ़ील्ड एक वैध ईमेल पता होना चाहिए।',
    'ends_with' => ':attribute फ़ील्ड को निम्नलिखित में से किसी के साथ समाप्त होना चाहिए: :values।',
    'enum' => 'चुना गया :attribute अवैध है।',
    'exists' => 'चुना गया :attribute अवैध है।',
    'extensions' => ':attribute फ़ील्ड को निम्नलिखित एक्सटेंशन में से एक होना चाहिए: :values।',
    'file' => ':attribute फ़ील्ड को एक फ़ाइल होना चाहिए।',
    'filled' => ':attribute फ़ील्ड में एक मान होना चाहिए।',
    'gt' => [
        'array' => ':attribute फ़ील्ड में :value से अधिक आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड का आकार :value किलोबाइट्स से अधिक होना चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value से अधिक होना चाहिए।',
        'string' => ':attribute फ़ील्ड में :value से अधिक कैरेक्टर होने चाहिए।',
    ],
    'gte' => [
        'array' => ':attribute फ़ील्ड में :value या अधिक आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड का आकार :value किलोबाइट्स या अधिक होना चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value या अधिक होना चाहिए।',
        'string' => ':attribute फ़ील्ड में :value या अधिक कैरेक्टर होने चाहिए।',
    ],
    'hex_color' => ':attribute फ़ील्ड एक वैध हेक्साडेसिमल रंग होना चाहिए।',
    'image' => ':attribute फ़ील्ड एक चित्र होना चाहिए।',
    'in' => 'चुना गया :attribute अवैध है।',
    'in_array' => ':attribute फ़ील्ड को :other में होना चाहिए।',
    'integer' => ':attribute फ़ील्ड को एक पूर्णांक होना चाहिए।',
    'ip' => ':attribute फ़ील्ड को एक वैध IP पता होना चाहिए।',
    'ipv4' => ':attribute फ़ील्ड को एक वैध IPv4 पता होना चाहिए।',
    'ipv6' => ':attribute फ़ील्ड को एक वैध IPv6 पता होना चाहिए।',
    'json' => ':attribute फ़ील्ड को एक वैध JSON स्ट्रिंग होना चाहिए।',
    'list' => ':attribute फ़ील्ड को एक सूची होना चाहिए।',
    'lowercase' => ':attribute फ़ील्ड को लोअरकेस होना चाहिए।',
    'lt' => [
        'array' => ':attribute फ़ील्ड में :value से कम आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड का आकार :value किलोबाइट्स से कम होना चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value से कम होना चाहिए।',
        'string' => ':attribute फ़ील्ड में :value से कम कैरेक्टर होने चाहिए।',
    ],
    'lte' => [
        'array' => ':attribute फ़ील्ड में :value या कम आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड का आकार :value किलोबाइट्स या कम होना चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value या कम होना चाहिए।',
        'string' => ':attribute फ़ील्ड में :value या कम कैरेक्टर होने चाहिए।',
    ],
    'mac_address' => ':attribute फ़ील्ड को एक वैध MAC पता होना चाहिए।',
    'max' => [
        'array' => ':attribute फ़ील्ड में :max से अधिक आइटम नहीं होने चाहिए।',
        'file' => ':attribute फ़ील्ड का आकार :max किलोबाइट्स से अधिक नहीं होना चाहिए।',
        'numeric' => ':attribute फ़ील्ड :max से अधिक नहीं होना चाहिए।',
        'string' => ':attribute फ़ील्ड में :max से अधिक कैरेक्टर नहीं होने चाहिए।',
    ],
    'max_digits' => ':attribute फ़ील्ड में :max से अधिक अंक नहीं होने चाहिए।',
    'mimes' => ':attribute फ़ील्ड को प्रकार की फ़ाइल होनी चाहिए: :values।',
    'mimetypes' => ':attribute फ़ील्ड को प्रकार की फ़ाइल होनी चाहिए: :values।',
    'min' => [
        'array' => ':attribute फ़ील्ड में कम से कम :min आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड का आकार कम से कम :min किलोबाइट्स होना चाहिए।',
        'numeric' => ':attribute फ़ील्ड कम से कम :min होना चाहिए।',
        'string' => ':attribute फ़ील्ड में कम से कम :min कैरेक्टर होने चाहिए।',
    ],
    'min_digits' => ':attribute फ़ील्ड में कम से कम :min अंक होने चाहिए।',
    'missing' => ':attribute फ़ील्ड गायब होना चाहिए।',
    'missing_if' => ':attribute फ़ील्ड गायब होना चाहिए जब :other :value हो।',
    'missing_unless' => ':attribute फ़ील्ड गायब होना चाहिए जब तक :other :value न हो।',
    'missing_with' => ':attribute फ़ील्ड गायब होना चाहिए जब :values मौजूद हों।',
    'missing_with_all' => ':attribute फ़ील्ड गायब होना चाहिए जब :values मौजूद हों।',
    'multiple_of' => ':attribute फ़ील्ड को :value का गुणांक होना चाहिए।',
    'not_in' => 'चुना गया :attribute अवैध है।',
    'not_regex' => ':attribute फ़ील्ड का प्रारूप अवैध है।',
    'numeric' => ':attribute फ़ील्ड को एक संख्या होना चाहिए।',
    'password' => [
        'letters' => ':attribute फ़ील्ड में कम से कम एक अक्षर होना चाहिए।',
        'mixed' => ':attribute फ़ील्ड में कम से कम एक बड़ा और एक छोटा अक्षर होना चाहिए।',
        'numbers' => ':attribute फ़ील्ड में कम से कम एक अंक होना चाहिए।',
        'symbols' => ':attribute फ़ील्ड में कम से कम एक प्रतीक होना चाहिए।',
        'uncompromised' => 'दिया गया :attribute पहले ही उल्लंघन में है। कृपया एक अलग :attribute चुनें।',
    ],
    'present' => ':attribute फ़ील्ड मौजूद होना चाहिए।',
    'present_if' => 'जब :other :value हो तो :attribute फ़ील्ड अवश्य मौजूद होना चाहिए।',
    'present_unless' => ':attribute फ़ील्ड तब तक मौजूद रहना चाहिए जब तक :other :value न हो।',
    'present_with' => ':values ​​मौजूद होने पर :attribute फ़ील्ड मौजूद होना चाहिए।',
    'present_with_all' => 'जब :values ​​मौजूद हों तो :attribute फ़ील्ड मौजूद होना चाहिए.जब :values ​​मौजूद हों तो :attribute फ़ील्ड मौजूद होना चाहिए.',
    'prohibited' => ':attribute फ़ील्ड प्रतिबंधित है।',
    'prohibited_if' => ':attribute फ़ील्ड को :other के :value होने पर प्रतिबंधित किया गया है।',
    'prohibited_unless' => ':attribute फ़ील्ड को :other के :value होने तक प्रतिबंधित किया गया है।',
    'prohibits' => ':attribute फ़ील्ड :other को उपस्थित होने से रोकता है।',
    'regex' => ':attribute फ़ील्ड का प्रारूप अवैध है।',
    'required' => ':attribute फ़ील्ड आवश्यक है।',
    'required_array_keys' => ':attribute फ़ील्ड में :values ​​के लिए प्रविष्टियाँ होनी चाहिए।',
    'required_if' => ':attribute फ़ील्ड आवश्यक है जब :other :value हो।',
    'required_if_accepted' => 'जब :other स्वीकार किया जाता है तो :attribute फ़ील्ड आवश्यक है।',
    'required_unless' => ':attribute फ़ील्ड आवश्यक है जब तक :other :value न हो।',
    'required_with' => ':attribute फ़ील्ड आवश्यक है जब :values मौजूद हों।',
    'required_with_all' => ':attribute फ़ील्ड आवश्यक है जब :values मौजूद हों।',
    'required_without' => ':attribute फ़ील्ड आवश्यक है जब :values मौजूद न हों।',
    'required_without_all' => ':attribute फ़ील्ड आवश्यक है जब कोई भी :values मौजूद न हों।',
    'same' => ':attribute और :other मेल खाने चाहिए।',
    'size' => [
        'array' => ':attribute फ़ील्ड में :size आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड का आकार :size किलोबाइट्स होना चाहिए।',
        'numeric' => ':attribute फ़ील्ड :size होना चाहिए।',
        'string' => ':attribute फ़ील्ड में :size कैरेक्टर होने चाहिए।',
    ],
    'starts_with' => ':attribute फ़ील्ड को निम्नलिखित में से किसी के साथ शुरू होना चाहिए: :values।',
    'string' => ':attribute फ़ील्ड को एक स्ट्रिंग होना चाहिए।',
    'timezone' => ':attribute फ़ील्ड एक वैध टाइम ज़ोन होना चाहिए।',
    'unique' => ':attribute पहले ही लिया जा चुका है।',
    'uploaded' => ':attribute को अपलोड करने में विफल।',
    'uppercase' => ':attribute फ़ील्ड बड़े अक्षरों में होनी चाहिए।',
    'url' => ':attribute फ़ील्ड एक वैध URL होना चाहिए।',
    'ulid' => ':attribute फ़ील्ड एक वैध ULID होना चाहिए।',
    'uuid' => ':attribute फ़ील्ड को एक वैध UUID होना चाहिए।',

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
            'rule-name' => 'सीमा शुल्क संदेश',
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
