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

    'accepted' => 'Kolom :attribute harus diterima.',
    'accepted_if' => 'Kolom :attribute harus diterima jika :other adalah :value.',
    'active_url' => 'Kolom :attribute harus berupa URL yang valid.',
    'after' => 'Kolom :attribute harus berupa tanggal setelah :date.',
    'after_or_equal' => 'Kolom :attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha' => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Kolom :attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'array' => 'Kolom :attribute harus berupa array.',
    'ascii' => 'Kolom :attribute hanya boleh berisi karakter alfanumerik dan simbol satu byte.',
    'before' => 'Kolom :attribute harus berupa tanggal sebelum :date.',
    'before_or_equal' => 'Kolom :attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => 'Kolom :attribute harus memiliki antara :min dan :max item.',
        'file' => 'Kolom :attribute harus antara :min dan :max kilobyte.',
        'numeric' => 'Kolom :attribute harus antara :min dan :max.',
        'string' => 'Kolom :attribute harus memiliki panjang diantara :min dan :max karakter.',
    ],
    'boolean' => 'Kolom :attribute harus true atau false.',
    'can' => 'Kolom :attribute berisi nilai yang tidak sah.',
    'confirmed' => 'Kolom konfirmasi :attribute tidak sesuai.',
    'current_password' => 'Kata sandi salah.',
    'date' => 'Kolom :attribute harus berupa tanggal yang valid.',
    'date_equals' => 'Kolom :attribute harus berupa tanggal yang sama dengan :date.',
    'date_format' => 'Kolom :attribute harus sesuai dengan format :format.',
    'decimal' => 'Kolom :attribute harus memiliki :decimal angka di belakang koma.',
    'declined' => 'Kolom :attribute harus ditolak.',
    'declined_if' => 'Kolom :attribute harus ditolak jika :other adalah :value.',
    'different' => 'Kolom :attribute dan :other harus berbeda.',
    'digits' => 'Kolom :attribute harus terdiri dari :digits digit.',
    'digits_between' => 'Kolom :attribute harus terdiri dari antara :min dan :max digit.',
    'dimensions' => 'Kolom :attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => 'Kolom :attribute memiliki nilai duplikat.',
    'doesnt_end_with' => 'Kolom :attribute tidak boleh diakhiri dengan salah satu dari berikut ini: :values.',
    'doesnt_start_with' => 'Kolom :attribute tidak boleh dimulai dengan salah satu dari berikut ini: :values.',
    'email' => 'Kolom :attribute harus alamat email yang valid.',
    'ends_with' => 'Kolom :attribute harus diakhiri dengan salah satu dari berikut: :values.',
    'enum' => 'Kolom :attribute yang telah dipilih tidak valid.',
    'exists' => 'Kolom :attribute yang telah dipilih tidak valid.',
    'extensions' => 'Kolom :attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file' => 'Kolom :attribute harus berupa sebuah file.',
    'filled' => 'Kolom :attribute harus memiliki nilai.',
    'gt' => [
        'array' => 'Kolom :attribute harus berjumlah lebih dari :value item.',
        'file' => 'Kolom :attribute harus memiliki ukuran yang lebih besar dari :value kilobytes.',
        'numeric' => 'Kolom :attribute harus lebih besar dari :value.',
        'string' => 'Kolom :attribute harus lebih panjang dari :value karakter.',
    ],
    'gte' => [
        'array' => 'Kolom :attribute harus memiliki :value item atau lebih.',
        'file' => 'Kolom :attribute harus lebih besar dari atau sama dengan :value kilobyte.',
        'numeric' => 'Kolom :attribute harus lebih dari atau sama dengan :value.',
        'string' => 'Kolom :attribute harus lebih atau sama dengan :value karakter.',
    ],
    'hex_color' => 'Kolom :attribute harus berupa kode warna heksadesimal yang valid.',
    'image' => 'Kolom :attribute harus berupa gambar.',
    'in' => 'Kolom :attribute yang telah dipilih tidak valid.',
    'in_array' => 'Kolom :attribute harus tersedia di :other.',
    'integer' => 'Kolom :attribute harus berupa angka.',
    'ip' => 'Kolom :attribute harus berupa alamat IP.',
    'ipv4' => 'Kolom :attribute harus berupa alamat IPv4.',
    'ipv6' => 'Kolom :attribute harus berupa alamat IPv6.',
    'json' => 'Kolom :attribute harus berupa string JSON.',
    'list' => 'Kolom :attribute harus berupa sebuah list.',
    'lowercase' => 'Kolom :attribute harus huruf kecil.',
    'lt' => [
        'array' => 'Kolom :attribute harus berjumlah kurang dari :value item.',
        'file' => 'Kolom :attribute harus memiliki ukuran yang lebih kecil dari :value kilobytes.',
        'numeric' => 'Kolom :attribute harus lebih kecil dari :value.',
        'string' => 'Kolom :attribute harus harus lebih pendek dari :value karakter.',
    ],
    'lte' => [
        'array' => 'Kolom :attribute harus memiliki :value item atau kurang.',
        'file' => 'Kolom :attribute harus lebih kecil atau sama dengan :value kilobyte.',
        'numeric' => 'Kolom :attribute harus lebih kecil atau sama dengan :value.',
        'string' => 'Kolom :attribute harus lebih kecil atau sama dengan :value karakter.',
    ],
    'mac_address' => 'Kolom :attribute harus berupa alamat MAC.',
    'max' => [
        'array' => 'Kolom :attribute tidak boleh lebih dari :max item.',
        'file' => 'Kolom :attribute tidak boleh lebih dari :max kilobyte.',
        'numeric' => 'Kolom :attribute tidak boleh lebih dari :max.',
        'string' => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
    ],
    'max_digits' => 'Kolom :attribute tidak boleh lebih dari :max digit.',
    'mimes' => 'Kolom :attribute harus berupa file dengan tipe: values.',
    'mimetypes' => 'Kolom :attribute harus berupa file dengan tipe: values.',
    'min' => [
        'array' => 'Kolom :attribute tidak boleh kurang dari :min item.',
        'file' => 'Kolom :attribute tidak boleh kurang dari :min kilobyte.',
        'numeric' => 'Kolom :attribute tidak boleh kurang dari :min.',
        'string' => 'Kolom :attribute tidak boleh kurang dari :min karakter.',
    ],
    'min_digits' => 'Kolom :attribute tidak boleh kurang dari :min digit.',
    'missing' => 'Kolom :attribute harus kosong.',
    'missing_if' => 'Kolom :attribute harus kosong ketika :other adalah :value.',
    'missing_unless' => 'Kolom :attribute harus kosong kecuali :other adalah :value.',
    'missing_with' => 'Kolom :attribute harus kosong ketika :values ada.',
    'missing_with_all' => 'Kolom :attribute harus kosong ketika :values ada.',
    'multiple_of' => 'Kolom :attribute harus kelipatan dari :value.',
    'not_in' => 'Kolom :attribute yang telah dipilih tidak valid.',
    'not_regex' => 'Kolom :attribute memiliki format yang tidak valid.',
    'numeric' => 'Kolom :attribute harus berupa angka.',
    'password' => [
        'letters' => 'Kolom :attribute harus berisi minimal satu huruf.',
        'mixed' => 'Kolom :attribute harus berisi minimal satu huruf besar dan satu huruf kecil.',
        'numbers' => 'Kolom :attribute harus berisi minimal satu angka.',
        'symbols' => 'Kolom :attribute harus berisi minimal satu simbol.',
        'uncompromised' => 'Kolom :attribute yang diberikan muncul dalam kebocoran data. Silahkan pilih :attribute yang lain.',
    ],
    'present' => 'Kolom :attribute harus ada.',
    'present_if' => 'Kolom :attribute harus ada saat :other adalah :value.',
    'present_unless' => 'Kolom :attribute harus ada kecuali :other adalah :value.',
    'present_with' => 'Kolom :attribute harus ada saat :values ada.',
    'present_with_all' => 'Kolom :attribute harus ada saat :values ada.',
    'prohibited' => 'Kolom :attribute dilarang.',
    'prohibited_if' => 'Kolom :attribute dilarang saat :other adalah :value.',
    'prohibited_unless' => 'Kolom :attribute dilarang kecuali :other ada di dalam :values.',
    'prohibits' => 'Kolom :attribute melarang :other untuk ada.',
    'regex' => 'Kolom :attribute memiliki format yang tidak valid.',
    'required' => 'Kolom :attribute harus diisi.',
    'required_array_keys' => 'Kolom :attribute harus berisi entri untuk: :values.',
    'required_if' => 'Kolom :attribute harus diisi saat :other adalah :value.',
    'required_if_accepted' => 'Kolom :attribute harus diisi saat :other diterima.',
    'required_unless' => 'Kolom :attribute harus diisi kecuali :other adalah :values.',
    'required_with' => 'Kolom :attribute harus diisi saat :values ​​ada.',
    'required_with_all' => 'Kolom :attribute harus diisi saat :values ​​ada.',
    'required_without' => 'Kolom :attribute harus diisi saat :values tidak ​​ada.',
    'required_without_all' => 'Kolom :attribute harus diisi saat :values ​​tidak ada.',
    'same' => 'Kolom :attribute harus cocok dengan :other.',
    'size' => [
        'array' => 'Kolom :attribute harus berisi :size item.',
        'file' => 'Kolom :attribute harus berukuran :size kilobyte.',
        'numeric' => 'Kolom :attribute harus bernilai :size.',
        'string' => 'Kolom :attribute harus berjumlah :size karakter.',
    ],
    'starts_with' => 'Kolom :attribute harus dimulai dengan salah satu: values.',
    'string' => 'Kolom :attribute harus berupa string.',
    'timezone' => 'Kolom :attribute harus berupa zona waktu.',
    'unique' => 'Kolom :attribute sudah digunakan. ',
    'uploaded' => 'Kolom :attribute gagal diunggah.',
    'uppercase' => 'Kolom :attribute harus huruf besar.',
    'url' => 'Kolom :attribute harus berupa URL.',
    'ulid' => 'Kolom :attribute harus berupa ULID',
    'uuid' => 'Kolom :attribute harus berupa UUID.',

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
