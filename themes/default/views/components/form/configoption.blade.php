<div class="flex flex-col gap-1">
    @switch($config->type)
        @case('select')
            <x-form.select name="{{ $name }}" :label="__($config->label ?? $config->name)" :required="$config->required ?? false"
                :selected="config('configs.' . $config->name)" :multiple="$config->multiple ?? false"
                wire:model.live="{{ $name }}">
                {{ $slot }}
            </x-form.select>
        @break

        @case('text')
        @case('password')

        @case('email')
        @case('number')

        @case('color')
        @case('file')
            <x-form.input name="{{ $name }}" :type="$config->type" :label="__($config->label ?? $config->name)"
                :placeholder="$config->default ?? ''" :required="$config->required ?? false" wire:model.live="{{ $name }}" />
        @break

        @case('checkbox')
            <x-form.checkbox name="{{ $name }}" type="checkbox" :label="__($config->label ?? $config->name)"
                :required="$config->required ?? false" :checked="config('configs.' . $config->name) ? true : false" wire:model="{{ $name }}" />
        @break
        
        @case('radio')
            <x-form.radio name="{{ $name }}" :label="__($config->label ?? $config->name)"
                :selected="config('configs.' . $config->name)" :required="$config->required ?? false" wire:model="{{ $name }}">
                {{ $slot }}
            </x-form.radio>
        @break

        @default
    @endswitch
    @isset($config->description)
        @isset($config->link)
            <a href="{{ $config->link }}" class="text-xs text-primary-500 hover:underline hover:text-secondary group">
                {{ $config->description }}
                <!-- Arrow to right top -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="ml-1 h-3 w-3 inline-block -rotate-45 group-hover:rotate-0 transition" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        @else
            <p class="text-xs text-primary-500">{{ $config->description }}</p>
        @endisset
    @endisset
</div>
