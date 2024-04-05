<x-admin-layout>
    <x-slot name="title">
        {{ __('Editing ') . $product->name }}
    </x-slot>
    <div class="h-full mx-auto">
        <div class="pb-6 bg-white dark:bg-secondary-100 dark:border-darkmode">
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
                <div class="flex-none">
                    <a href="{{ route('admin.products.upgrade', $product->id) }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-y-transparent hover:border-logo hover:text-logo">
                        {{ __('Upgrades') }}
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
                class="dark:hover:bg-darkmode absolute top-0 right-0 dark:text-darkmodetext dark:bg-secondary-100 inline-flex w-max justify-end bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 mr-4"
                id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="moreOptions">
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z">
                    </path>
                </svg>
            </button>
            <div class="absolute hidden w-max origin-top-right bg-white rounded-md shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5 z-20"
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
    <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">
        <form method="POST" action="{{ route('admin.products.extension.update', $product->id) }}"
            enctype="multipart/form-data" id="formu">
            @csrf
            <div>
                <label for="server">{{ __('Server') }}</label>
                <div class="flex">
                    <select id="server"
                        class="block w-full rounded-md shadow-sm focus:ring-logo focus:border-logo sm:text-sm dark:bg-darkmode"
                        name="extension_id" required onchange="document.getElementById('submitt').disabled = false;">
                        @if ($extensions->count())
                            <option value="" disabled selected>None</option>
                            @foreach ($extensions as $server)
                                @if ($server->id == $product->extension_id)
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
                <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-secondary-100 grid grid-cols-2 gap-x-2">
                    @foreach ($extension->productConfig as $setting)
                        @if (!isset($setting->required))
                            @php
                                $setting->required = false;
                            @endphp
                        @endif
                        @if ($setting->type == 'title')
                            <div class="mt-4 col-span-2">
                                <div class="text-xl dark:text-darkmodetext">
                                    {{ $setting->friendlyName }}
                                </div>
                                <p class="text-gray-500 dark:text-darkmodetext">
                                    {{ $setting->description }}
                                </p>
                            </div>
                            @continue
                        @endif
                        <div class="mt-4">
                            <x-config-item :config="$setting" />
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
