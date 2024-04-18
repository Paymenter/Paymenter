<form id="settings"
    wire:submit.prevent="save" x-data="{ tab: 'general' }">
    <h1 class="text-2xl text-center mt-2">{{ __('Settings') }} </h1>

    <div
        class="text-sm font-medium text-center border-b text-gray-400 border-gray-700">
        <ul class="flex -mb-px overflow-auto">
            @foreach ($settings as $key => $categories)
                <li class="mr-2" x-init="if (window.location.hash == '#' + '{{ $key }}') tab = '{{ $key }}'">
                    <button type="button"
                        x-on:click="tab = '{{ $key }}'; window.location.hash = '{{ $key }}'"
                        :class="tab == '{{ $key }}' ?
                            'border-blue-500 text-blue-500' :
                            'text-gray-500 hover:border-gray-300 hover:text-gray-300'"
                        class="inline-block p-4 rounded-t-lg border-b-2 border-transparent whitespace-nowrap	">
                        {{ ucwords(str_replace('-', ' ', $key)) }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="grid md:grid-cols-2 gap-x-4 gap-y-2 mt-2">
        @foreach ($settings as $key => $categories)
            @foreach ($categories as $setting)
                <div x-cloak x-show="tab == '{{ $key }}'" class="{{ $setting->type == 'checkbox' ? 'col-span-2' : '' }}">
                    <x-form.setting :$setting :$key />
                </div>
            @endforeach
        @endforeach
    </div>
    <div class="w-min">
        <x-button.save type="submit">{{ __('Save') }}</x-button.save>
    </div>
</form>
