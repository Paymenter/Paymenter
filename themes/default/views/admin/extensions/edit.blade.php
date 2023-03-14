<x-admin-layout>
    <x-slot name="title">
        {{ __('Edit') }}
    </x-slot>

    <div class="mt-8 text-2xl dark:text-darkmodetext">
        {{ __('Edit') }} {{ $extension->name }}
    </div>

    <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
        <form method="POST" action="{{ route('admin.extensions.update', [$extension->type, $extension->name]) }}">
            @csrf
            <!-- disable or enable extension -->
            <div class="mt-4">
                <label for="enabled">{{ __('Enabled') }}</label>
                <select id="enabled"
                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                    name="enabled" required>
                    @if ($extension->enabled == 1)
                        <option value="1" selected>True</option>
                        <option value="0">False</option>
                    @else
                        <option value="1">True</option>
                        <option value="0" selected>False</option>
                    @endif
                </select>
            </div>
            <div class="mt-4">
                <label for="display_name">{{ __('Display Name') }}</label>
                <input id="display_name" type="text"
                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                    name="display_name"
                    value="{{ isset($extension->display_name) ? $extension->display_name : $extension->name }}"
                    required />
            </div>
            @foreach ($extension->config as $setting)
                <div class="mt-4">
                    <label for="{{ $setting->name }}" class="flex items-center space-x-1">
                        <span>
                            {{ $setting->friendlyName }}
                        </span>
                        @isset($setting->description)
                            <span>
                                <svg width="16" height="16"
                                    class="w-5 h-5 mr-1 text-gray-400 transition duration-150 ease-in-out cursor-help fill-current dark:text-darkmodetext"
                                    data-tooltip-target="{{ $setting->name }}" aria-hidden="true" fill="currentColor"
                                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <div id="{{ $setting->name }}" role="tooltip"
                                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    {{ $setting->description }}
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </span>
                        @endisset
                    </label>
                    @if ($setting->type == 'text')
                        <input type="text" name="{{ $setting->name }}"
                            value="{{ App\Helpers\ExtensionHelper::getConfig($extension->name, $setting->name) }}"
                            class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                            @if ($setting->required) required @endif />
                    @elseif($setting->type == 'boolean')
                        <input type="checkbox" name="{{ $setting->name }}" value="1"
                            @if (App\Helpers\ExtensionHelper::getConfig($extension->name, $setting->name) == 1) checked @endif
                            class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                            @if ($setting->required) required @endif />
                    @elseif($setting->type == 'dropdown')
                        <select name="{{ $setting->name }}[]" multiple
                            class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                            @if ($setting->required) required @endif>
                            @foreach ($setting->options as $option)
                                <option value="{{ $option }}" @if (in_array($option, [App\Helpers\ExtensionHelper::getConfig($extension->name, $setting->name)])) selected @endif>
                                    {{ $option }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endforeach
            <div class="flex items-center justify-end mt-4">
                <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                    {{ __('Update') }}
                </button>
            </div>
        </form>
    </div>

</x-admin-layout>
