<div class="flex flex-col gap-1">
    @switch($setting->type)
        @case('select')
            <x-form.select name="fields.{{ $key }}.{{ $setting->name }}" :label="__($setting->label ?? $setting->name)" :required="$setting->required ?? false"
                :options="$setting->options" :selected="config('settings.' . $setting->name)" :multiple="$setting->multiple ?? false"
                wire:model="fields.{{ $key }}.{{ $setting->name }}" />
        @break

        @case('text')
        @case('password')

        @case('email')
        @case('number')

        @case('color')
        @case('file')
            <x-form.input name="fields.{{ $key }}.{{ $setting->name }}" :type="$setting->type" :label="__($setting->label ?? $setting->name)"
                :placeholder="$setting->default ?? ''" :required="$setting->required ?? false" wire:model="fields.{{ $key }}.{{ $setting->name }}" />
        @break

        @case('checkbox')
            <x-form.checkbox name="fields.{{ $key }}.{{ $setting->name }}" type="checkbox" :label="__($setting->label ?? $setting->name)"
                :required="$setting->required ?? false" :checked="config('settings.' . $setting->name) ? true : false" wire:model="fields.{{ $key }}.{{ $setting->name }}" />
        @break

        @default
    @endswitch
    @isset($setting->description)
        @isset($setting->link)
            <a href="{{ $setting->link }}" class="text-xs text-primary-500 hover:underline hover:text-secondary group">
                {{ $setting->description }}
                <!-- Arrow to right top -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="ml-1 h-3 w-3 inline-block -rotate-45 group-hover:rotate-0 transition" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        @else
            <p class="text-xs text-primary-500">{{ $setting->description }}</p>
        @endisset
    @endisset
</div>
