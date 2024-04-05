<x-admin-layout title="Upgrades">
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
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-y-transparent hover:border-logo hover:text-logo">
                        {{ __('Extension') }}
                    </a>
                </div>
                <div class="flex-none">
                    <a href="{{ route('admin.products.upgrade', $product->id) }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-logo text-logo">
                        {{ __('Upgrades') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('admin.products.upgrade.update', $product->id) }}" method="POST">
        @csrf
        <div >
            <h1 class="font-semibold text-2xl text-gray-900 dark:text-darkmodetext">
                {{ __('Upgrades') }}
            </h1>

            <x-input type="select" multiple name="upgrades[]" class="mt-2">
                @foreach ($products as $product2)
                    <option value="{{ $product2->id }}" @if ($product->upgrades()->where('upgrade_product_id', $product2->id)->exists()) selected @endif>{{ $product2->name }}</option>
                @endforeach
            </x-input>

            <div class="hidden tobereleased">
                <x-input class="mt-2" type="checkbox" value="1" name="upgrade_configurable_options" label="{{ __('Allow upgrade of configurable options') }}" :checked="$product->upgrade_configurable_options ? true : false" />
            </div>

            <button class="button button-primary mt-4">{{ __('Save') }}</button>
        </div>
    </form>

</x-admin-layout>