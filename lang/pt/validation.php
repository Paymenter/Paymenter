<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validação de idioma
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de idioma contêm as mensagens de erro padrão usadas pela
    | classe validadora. Algumas dessas regras têm várias versões, como
    | regras de tamanho. Sinta-se à vontade para modificar cada uma dessas mensagens aqui.
    |
    */

    'accepted' => 'O campo :attribute deve ser aceite.',
'accepted_if' => 'O campo :attribute deve ser aceite quando :other é :value.',
'active_url' => 'O campo :attribute não é um URL válido.',
'after' => 'O campo :attribute deve ser uma data depois de :date.',
'after_or_equal' => 'O campo :attribute deve ser uma data depois ou igual a :date.',
'alpha' => 'O campo :attribute só pode conter letras.',
'alpha_dash' => 'O campo :attribute só pode conter letras, números e traços.',
'alpha_num' => 'O campo :attribute só pode conter letras e números.',
'array' => 'O campo :attribute deve ser uma matriz.',
'ascii' => 'O :attribute só deve conter símbolos e caracteres alfanuméricos de um único byte.',
'before' => 'O campo :attribute deve ser uma data antes de :date.',
'before_or_equal' => 'O campo :attribute deve ser uma data antes ou igual a :date.',
'between' => [
'numeric' => 'O campo :attribute deve estar entre :min - :max.',
'file' => 'O campo :attribute deve estar entre :min - :max kilobytes.',
'string' => 'O campo :attribute deve estar entre :min - :max caracteres.',
'array' => 'O campo :attribute deve ter entre :min e :max elementos.',
],
'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
'confirmed' => 'O campo de confirmação de :attribute não coincide.',
'current_password' => 'A senha atual não é correta',
'date' => 'O campo :attribute não é uma data válida.',
'date_equals' => 'O campo :attribute deve ser uma data igual a :date.',
'date_format' => 'O campo :attribute não corresponde com o formato :format.',
'decimal' => 'O :attribute deve ter :decimal decimais.',
'declined' => 'O campo :attribute deve ser marcado como rejeitado.',
'declined_if' => 'O campo :attribute deve ser marcado como rejeitado quando :other é :value.',
'different' => 'Os campos :attribute e :other devem ser diferentes.',
'digits' => 'O campo :attribute deve ter :digits dígitos.',
'digits_between' => 'O campo :attribute deve ter entre :min e :max dígitos.',
'dimensions' => 'O campo :attribute não tem uma dimensão válida.',
'distinct' => 'O campo :attribute tem um valor duplicado.',
'doesnt_end_with' => 'O campo :attribute não pode terminar com um dos seguintes valores: :values.',
'doesnt_start_with' => 'O campo :attribute não pode começar com um dos seguintes valores: :values.',
'email' => 'O formato do :attribute não é válido.',
'ends_with' => 'O campo :attribute deve terminar com um dos valores: :values.',
'enum' => 'O campo selecionado em :attribute não é válido.',
'exists' => 'O campo selecionado em :attribute não é válido.',
'file' => 'O campo :attribute deve ser um arquivo.',
'filled' => 'O campo :attribute é obrigatório.',
'gt' => [
    'numeric' => 'O campo :attribute deve ser maior que :value.',
    'file' => 'O campo :attribute deve ser maior que :value kilobytes.',
    'string' => 'O campo :attribute deve ser maior que :value caracteres.',
    'array' => 'O campo :attribute pode ter até :value elementos.',
    ],
    'gte' => [
    'numeric' => 'O campo :attribute deve ser maior ou igual a :value.',
    'file' => 'O campo :attribute deve ser maior ou igual a :value kilobytes.',
    'string' => 'O campo :attribute deve ser maior ou igual a :value caracteres.',
    'array' => 'O campo :attribute pode ter :value elementos ou mais.',
    ],
    'image' => 'O campo :attribute deve ser uma imagem.',
    'in' => 'O valor selecionado para o campo :attribute é inválido.',
    'in_array' => 'O campo :attribute não existe em :other.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'ip' => 'O campo :attribute deve ser um endereço IP válido.',
    'ipv4' => 'O campo :attribute deve ser um endereço IPv4 válido.',
    'ipv6' => 'O campo :attribute deve ser um endereço IPv6 válido.',
    'json' => 'O campo :attribute deve ser uma string JSON válida.',
    'lowercase' => 'O :attribute deve estar em minúsculas.',
    'lt' => [
    'numeric' => 'O campo :attribute deve ser menor que :max.',
    'file' => 'O campo :attribute deve ser menor que :max kilobytes.',
    'string' => 'O campo :attribute deve ter menos de :max caracteres.',
    'array' => 'O campo :attribute pode ter até :max elementos.',
    ],
    'lte' => [
    'numeric' => 'O campo :attribute deve ser menor ou igual a :max.',
    'file' => 'O campo :attribute deve ser menor ou igual a :max kilobytes.',
    'string' => 'O campo :attribute deve ter no máximo :max caracteres.',
    'array' => 'O campo :attribute não pode ter mais de :max elementos.',
    ],
    'mac_address' => 'O campo :attribute deve ser um endereço MAC válido.',
    'max' => [
    'numeric' => 'O campo :attribute deve ser menor que :max.',
    'file' => 'O campo :attribute deve ser menor que :max kilobytes.',
    'string' => 'O campo :attribute deve ter menos de :max caracteres.',
    'array' => 'O campo :attribute pode ter até :max elementos.',
    ],
    'max_digits' => 'O campo :attribute não pode ter mais de :max dígitos.',
    'mimes' => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes' => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'min' => [
    'numeric' => 'O campo :attribute deve ter pelo menos :min.',
    'file' => 'O campo :attribute deve ter pelo menos :min kilobytes.',
    'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
    'array' => 'O campo :attribute deve ter pelo menos :min elementos.',
    ],
    'min_digits' => 'O campo :attribute deve ter no mínimo :min dígitos.',
'missing' => 'O campo :attribute deve estar em falta.',
'missing_if' => 'O campo :attribute deve estar em falta quando :other é :value',
'missing_unless' => 'O campo :attribute deve estar em falta a menos que :other seja :value.',
'missing_with' => 'O campo :attribute deve estar em falta quando :values está presente.',
'missing_with_all' => 'O campo :attribute deve estar em falta quando :values estão presentes',
'multiple_of' => 'O campo :attribute deve ser um múltiplo de :value.',
'not_in' => 'A seleção no campo :attribute é inválida.',
'not_regex' => 'O formato do campo :attribute não é válido.',
'numeric' => 'O campo :attribute deve ser um número.',
'password' => [
'letters' => 'O campo :attribute deve conter pelo menos uma letra.',
'mixed' => 'O campo :attribute deve conter pelo menos uma letra maiúscula e uma minúscula.',
'numbers' => 'O campo :attribute deve conter pelo menos um número.',
'symbols' => 'O campo :attribute deve conter pelo menos um símbolo.',
'uncompromised' => 'O valor do campo :attribute aparece em algum vazamento de dados. Por favor, indique um valor diferente.',
],
'present' => 'O campo :attribute deve estar presente.',
'prohibited' => 'O campo :attribute não é permitido.',
'prohibited_if' => 'O campo :attribute não é permitido quando :other é :value.',
'prohibited_unless' => 'O campo :attribute não é permitido a menos que :other esteja em :values.',
'prohibits' => 'O campo :attribute não permite que :other esteja presente.',
'regex' => 'O formato do campo :attribute não é válido.',
'required' => 'O campo :attribute é obrigatório.',
'required_array_keys' => 'O campo :attribute deve conter entradas para: :values.',
'required_if' => 'O campo :attribute é obrigatório quando o campo :other é :value.',
'required_unless' => 'O campo :attribute é obrigatório a menos que :other esteja presente em :values.',
'required_with' => 'O campo :attribute é obrigatório quando :values está presente.',
'required_with_all' => 'O campo :attribute é obrigatório quando :values está presente.',
'required_without' => 'O campo :attribute é obrigatório quando :values não está presente.',
'required_without_all' => 'O campo :attribute é obrigatório quando nenhum dos :values está presente.',
'same' => 'O campo :attribute e :other devem coincidir.',
'size' => [
'numeric' => 'O campo :attribute deve ser :size.',
'file' => 'O campo :attribute deve ter :size kilobytes.',
'string' => 'O campo :attribute deve ter :size caracteres.',
'array' => 'O campo :attribute deve conter :size elementos.',
],
'starts_with' => ':attribute deve começar com um dos seguintes valores :values',
'string' => 'O campo :attribute deve ser uma string.',
'timezone' => 'O campo :attribute deve ser um fuso horário válido.',
'unique' => 'O campo :attribute já foi utilizado.',
'uploaded' => 'O campo :attribute não pôde ser carregado.',
'uppercase' => 'O :attribute deve estar em maiúsculas.',
'url' => 'O formato de :attribute não é válido.',
'ulid' => 'O :attribute deve ser um ULID válido.',
'uuid' => 'O :attribute deve ser um UUID válido.',

'password.mixed' => 'O :attribute deve conter pelo menos uma letra maiúscula e uma letra minúscula.',
'password.letters' => 'O :attribute deve conter pelo menos uma letra.',
'password.symbols' => 'O :attribute deve conter pelo menos um símbolo.',
'password.numbers' => 'O :attribute deve conter pelo menos um número.',
'password.uncompromised' => 'O atributo :attribute apareceu em um vazamento de dados. Por favor escolha um :attribute diferente.',


    /*
    |--------------------------------------------------------------------------
    | Idioma de validação personalizado
    |--------------------------------------------------------------------------
    |
    | Aqui pode especificar mensagens de validação personalizadas para atributos utilizando a
    | convenção "atributo.regra" para nomear as linhas. Isso torna fácil
    | especificar uma linha de idioma personalizada específica para uma determinada regra de atributo.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atributos de validação personalizados
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de idioma são usadas para substituir os marcadores de posição de atributo
    | por algo mais fácil de ler, como o endereço de e-mail.
    | em vez de "email". Isso ajuda a tornar as mensagens um pouco mais claras.
    |
    */

    'attributes' => [],

];
