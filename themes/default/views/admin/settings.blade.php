<form class="flex flex-col gap-2 shadow-sm p-6 m-8 bg-white dark:bg-primary-800 rounded-md" id="settings"
    wire:submit.prevent="save" x-data="{ tab: 'general' }">
    <h1 class="text-2xl text-center dark:text-white mt-2">{{ __('Settings') }} </h1>

    <div
        class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px">
            @foreach ($settings as $key => $categories)
                <li class="mr-2" x-init="if (window.location.hash == '#' + '{{ $key }}') tab = '{{ $key }}'">
                    <button type="button"
                        x-on:click="tab = '{{ $key }}'; window.location.hash = '{{ $key }}'"
                        :class="tab == '{{ $key }}' ?
                            'border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-500' :
                            'text-gray-500 hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'"
                        class="inline-block p-4 rounded-t-lg border-b-2 border-transparent ">
                        {{ ucwords(str_replace('-', ' ', $key)) }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    @foreach ($settings as $key => $categories)
        @foreach ($categories as $setting)
            <div x-cloak x-show="tab == '{{ $key }}'">
                @switch($setting->type)
                    @case('select')
                        <x-form.select name="fields.{{ $key }}.{{ $setting->name }}" :label="__($setting->label ?? $setting->name)"
                            :required="$setting->required ?? false" :options="$setting->options" :selected="config('settings.' . $setting->name)" :multiple="$setting->multiple ?? false"
                            wire:model="fields.{{ $key }}.{{ $setting->name }}" />
                    @break

                    @case('text')
                        <x-form.input name="fields.{{ $key }}.{{ $setting->name }}" type="text" :label="__($setting->label ?? $setting->name)"
                            :placeholder="$setting->default ?? ''" :required="$setting->required ?? false"
                            wire:model="fields.{{ $key }}.{{ $setting->name }}" />
                    @break

                    @case('checkbox')
                        <x-form.checkbox name="fields.{{ $key }}.{{ $setting->name }}" type="checkbox"
                            :label="__($setting->label ?? $setting->name)" :required="$setting->required ?? false" :checked="config('settings.' . $setting->name) ? true : false"
                            wire:model="fields.{{ $key }}.{{ $setting->name }}" />
                    @break

                    @default
                @endswitch
            </div>
        @endforeach
    @endforeach
    <div class="w-min">
        <x-button.primary type="submit">{{ __('Save') }}</x-button.primary>
    </div>
</form>
