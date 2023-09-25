<x-admin-layout>
    <x-slot name="title">
        {{ __('Edit') }}
    </x-slot>

    <div class="mt-8 text-2xl dark:text-darkmodetext">
        {{ __('Edit') }} {{ $extension->name }}
    </div>

    <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">
        <form method="POST" action="{{ route('admin.extensions.update', [$extension->type, $extension->name]) }}">
            @csrf
            <!-- disable or enable extension -->
            <div class="mt-4">
                <label for="enabled">{{ __('Enabled') }}</label>
                <x-input type="select" id="enabled" name="enabled" required>
                    @if ($extension->enabled == 1)
                        <option value="1" selected>True</option>
                        <option value="0">False</option>
                    @else
                        <option value="1">True</option>
                        <option value="0" selected>False</option>
                    @endif
                </x-input>
            </div>
            <div class="mt-4">
                <label for="display_name">{{ __('Display Name') }}</label>
                <x-input id="display_name" type="text"
                    name="display_name"
                    value="{{ isset($extension->display_name) ? $extension->display_name : $extension->name }}"
                    required />
            </div>
            @foreach ($extension->config as $setting)
                <div class="mt-4">
                    <x-config-item :config="$setting" />
                </div>
            @endforeach
            <div class="flex items-center justify-end mt-4">
                <button type="submit" class="inline-flex justify-center w-max float-right button button-primary">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </div>

</x-admin-layout>
