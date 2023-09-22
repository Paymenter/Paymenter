@if ($config->type == 'text' || $config->type == 'number' || $config->type == 'email' || $config->type == 'password')
    <x-input type="{{ $config->type }}" placeholder="{{ $config->placeholder ?? ucfirst($config->name) }}"
        name="{{ $config->name }}" id="{{ $config->name }}"
        label="{{ $config->friendlyName ?? ucfirst($config->name) }}" required />
@elseif($config->type == 'textarea')
    <x-input type="textarea" placeholder="{{ $config->placeholder ?? ucfirst($config->name) }}"
        name="{{ $config->name }}" id="{{ $config->name }}"
        label="{{ $config->friendlyName ?? ucfirst($config->name) }}" required />
@elseif($config->type == 'dropdown')
    <x-input type="select" label="{{ ucfirst($config->name) }}"
        name="{{ $config->name }}" id="{{ $config->name }}" required>
        @foreach ($config->options as $option)
            <option value="{{ $option->value }}" @if (old($config->name) == $option) selected @endif>
                {{ $option->name }}
            </option>
        @endforeach
    </x-input>
@endif
