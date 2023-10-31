@php
    if(!isset($config->value)) {
        $config->value = '';
    }
@endphp
@if ($config->type == 'text' || $config->type == 'number' || $config->type == 'email' || $config->type == 'password')
    <x-input type="{{ $config->type }}" placeholder="{{ $config->placeholder ?? ucfirst($config->name) }}"
        name="{{ $config->name }}" id="{{ $config->name }}"
        value="{{ old($config->name) ?? $config->value }}"
        label="{{ $config->friendlyName ?? ucfirst($config->name) }}"
        :required="isset($config->required) ? $config->required : false"
        {{ $attributes->only('wire:change') }}
        />
@elseif($config->type == 'textarea')
    <x-input type="textarea" placeholder="{{ $config->placeholder ?? ucfirst($config->name) }}"
        name="{{ $config->name }}" id="{{ $config->name }}"
        value="{{ old($config->name) ?? $config->value }}"
        label="{{ $config->friendlyName ?? ucfirst($config->name) }}"
        :required="isset($config->required) ? $config->required : false"
        {{ $attributes->only('wire:change') }}
         />
@elseif($config->type == 'dropdown')
    <x-input type="select" label="{{ ucfirst($config->friendlyName ?? $config->name) }}"
        name="{{ $config->name }}" id="{{ $config->name }}"
        :required="isset($config->required) ? $config->required : false"
        {{ $attributes->only('wire:change') }}>
        @foreach ($config->options as $option)
            <option value="{{ $option->value }}" @if (old($config->name) == $option || $config->value == $option->value) selected @endif>
                {{ $option->name }}
            </option>
        @endforeach
    </x-input>
@elseif($config->type == 'boolean')
    <x-input type="checkbox" :label="ucfirst($config->friendlyName ?? $config->name)"
        value="1"
        :name="$config->name" :id="$config->name" 
        :required="isset($config->required) ? $config->required : false"
        :checked="old($config->name) == 1 || $config->value == 1"
        {{ $attributes->only('wire:change') }} />
@endif

