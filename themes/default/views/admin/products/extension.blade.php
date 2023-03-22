<x-admin-layout>
    <x-slot name="title">
        {{ __('Editing ') . $product->name }}
    </x-slot>
    <div class="container h-full mx-auto">
        <div class="p-6 mx-auto bg-white max-w-7xl pl-0 dark:bg-darkmode2 dark:border-darkmode">
            <div class="flex flex-row overflow-x-auto lg:flex-wrap lg:space-x-1">
                <div class="flex-none">
                    <a href="{{ route('admin.products.edit', $product->id) }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-y-transparent hover:border-logo hover:text-logo">
                        {{ __('Details') }}
                    </a>
                </div>
                <div class="flex-none">
                    <a href="{{ route('admin.products.pricing', $product->id) }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-y-transparent hover:border-logo hover:text-logo">
                        {{ __('Pricing') }}
                    </a>
                </div>
                <div class="flex-none">
                    <a href="{{ route('admin.products.extension', $product->id) }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-logo text-logo">
                        {{ __('Extension') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 mt-4">
        <div class="text-2xl dark:text-darkmodetext">
            {{ __('Update product server') }}: {{ $product->name }}
        </div>
        <div class="relative inline-block text-left justify-end">
            <button type="button"
                class="dark:hover:bg-darkmode absolute top-0 right-0 dark:text-darkmodetext dark:bg-darkmode2 inline-flex w-max justify-end bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 mr-4"
                id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="moreOptions">
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z">
                    </path>
                </svg>
            </button>
            <div class="absolute hidden w-max origin-top-right bg-white rounded-md shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5 z-[1]"
                role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                id="moreOptions">
                <div class="py-1 grid grid-cols-1" role="none">
                    <a href="{{ route('admin.products.extension.export', $product->id) }}"
                        class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900"
                        role="menuitem" tabindex="-1" id="menu-item-0">
                        {{ __('Export') }}
                    </a>
                    <form method="post" action="{{ route('admin.products.extension.import', $product->id) }}"
                        enctype="multipart/form-data" accept="application/json"
                        class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 cursor-pointer"
                        role="menuitem" tabindex="-1" id="menu-item-0"
                        onclick="document.getElementById('importFile').click();">
                        @csrf
                        <label for="json">
                            {{ __('Import') }}
                        </label>
                        <input type="file" name="json" class="hidden" onchange="this.form.submit()"
                            id="importFile">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
        <form method="POST" action="{{ route('admin.products.extension.update', $product->id) }}"
            enctype="multipart/form-data" id="formu">
            @csrf
            <div>
                <label for="server">{{ __('Server') }}</label>
                <div class="flex">
                    <select id="server"
                        class="block w-full rounded-md shadow-sm focus:ring-logo focus:border-logo sm:text-sm dark:bg-darkmode"
                        name="server_id" required onchange="document.getElementById('submitt').disabled = false;">
                        @if ($extensions->count())
                            <option value="" disabled selected>None</option>
                            @foreach ($extensions as $server)
                                @if ($server->id == $product->server_id)
                                    <option value="{{ $server->id }}" selected>{{ $server->name }}
                                    </option>
                                @else
                                    <option value="{{ $server->id }}">{{ $server->name }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="">{{ __('No servers found') }}</option>
                        @endif
                    </select>
                    <button type="button" class="ml-2 form-submit text-sm w-40 disabled:cursor-not-allowed"
                        onclick="document.getElementById('formu').submit();" disabled id="submitt">
                        {{ __('Update server') }}
                    </button>
                </div>
            </div>
            @isset($extension)
                <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2 grid grid-cols-2 gap-x-2">
                    @foreach ($extension->productConfig as $setting)
                        @if (!isset($setting->required))
                            @php
                                $setting->required = false;
                            @endphp
                        @endif
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
                                    value="{{ App\Helpers\ExtensionHelper::getProductConfig($extension->name, $setting->name, $product->id) }}"
                                    @if ($setting->required) required @endif
                                    class="block w-full rounded-md shadow-sm focus:ring-logo focus:border-logo sm:text-sm dark:bg-darkmode" />
                            @elseif($setting->type == 'boolean')
                                <input type="checkbox" name="{{ $setting->name }}" value="1"
                                    @if ($setting->required) required @endif
                                    @if (App\Helpers\ExtensionHelper::getProductConfig($extension->name, $setting->name, $product->id) == 1) checked @endif
                                    class="block rounded-md shadow-sm focus:ring-logo focus:border-logo sm:text-sm dark:bg-darkmode" />
                            @elseif($setting->type == 'dropdown')
                                <select name="{{ $setting->name }}" @if ($setting->required) required @endif
                                    class="block w-full rounded-md shadow-sm focus:ring-logo focus:border-logo sm:text-sm dark:bg-darkmode">
                                    @foreach ($setting->options as $option)
                                        <option value="{{ $option->value }}"
                                            @if (App\Helpers\ExtensionHelper::getProductConfig($extension->name, $setting->name, $product->id) == $option->value) selected @endif>
                                            {{ $option->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endisset

            <div class="flex items-center justify-end mt-4" type="submit">
                <button class="form-submit">
                    {{ __('Update') }}
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
