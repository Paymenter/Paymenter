<x-admin-layout>
    <x-slot name="title">
        {{ __('Pricing') }}
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
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-logo text-logo">
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
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-y-transparent hover:border-logo hover:text-logo">
                        {{ __('Upgrades') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Show pricing choice: free, one-time, subscription -->
    <form action="{{ route('admin.products.pricing.update', $product->id) }}" method="POST">
        @csrf
        <div class="mt-4">
            <h1 class="font-semibold text-2xl text-gray-900 dark:text-darkmodetext">
                {{ __('Pricing') }}
            </h1>
            <div class="mt-4">
                <div class="flex flex-row text-sm">
                    <div class="flex flex-col">
                        <label class="text-gray-700 dark:text-darkmodetext" for="pricing">
                            {{ __('Pricing') }}
                        </label>
                        <select name="pricing" id="pricing" class="form-input" onchange="this.form.submit()">
                            <option value="free" @if ($pricing->type == 'free') selected @endif>
                                {{ __('Free') }}
                            </option>
                            <option value="one-time" @if ($pricing->type == 'one-time') selected @endif>
                                {{ __('One-time') }}
                            </option>
                            <option value="recurring" @if ($pricing->type == 'recurring') selected @endif>
                                {{ __('Recurring') }}
                            </option>
                        </select>
                    </div>
                </div>
                @if ($pricing->type == 'one-time')
                    <div class="flex flex-row text-sm">
                        <div class="flex flex-col">
                            <label class="text-gray-700 dark:text-darkmodetext" for="price">
                                {{ __('Price') }}
                            </label>
                            <input type="text" name="monthly" id="price" value="{{ $pricing->monthly }}"
                                class="form-input">
                        </div>
                    </div>
                @elseif($pricing->type == 'recurring')
                    <div class="flex flex-row text-sm gap-4 mt-2">
                        <div class="flex flex-col">
                            <label class="text-gray-700 dark:text-darkmodetext" for="monthly">
                                {{ __('Monthly') }}
                            </label>
                            <input type="text" name="monthly" id="monthly" value="{{ $pricing->monthly }}"
                                class="form-input">
                            <label class="text-gray-700 dark:text-darkmodetext" for="monthly_setup">
                                {{ __('Monthly Setup Fee') }}
                            </label>
                            <input type="text" name="monthly_setup" id="monthly_setup_fee"
                                value="{{ $pricing->monthly_setup }}" class="form-input">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-700 dark:text-darkmodetext" for="quarterly">
                                {{ __('Quarterly') }}
                            </label>
                            <input type="text" name="quarterly" id="quarterly" value="{{ $pricing->quarterly }}"
                                class="form-input">
                            <label class="text-gray-700 dark:text-darkmodetext" for="quarterly_setup">
                                {{ __('Quarterly Setup Fee') }}
                            </label>
                            <input type="text" name="quarterly_setup" id="quarterly_setup_fee"
                                value="{{ $pricing->quarterly_setup }}" class="form-input">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-700 dark:text-darkmodetext" for="semiannually">
                                {{ __('Semiannually') }}
                            </label>
                            <input type="text" name="semi_annually" id="semi_annually"
                                value="{{ $pricing->semi_annually }}" class="form-input">
                            <label class="text-gray-700 dark:text-darkmodetext" for="semi_annually_setup">
                                {{ __('Semiannually Setup Fee') }}
                            </label>
                            <input type="text" name="semi_annually_setup" id="semi_annually_setup_fee"
                                value="{{ $pricing->semi_annually_setup }}" class="form-input">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-700 dark:text-darkmodetext" for="annually">
                                {{ __('Annually') }}
                            </label>
                            <input type="text" name="annually" id="annually" value="{{ $pricing->annually }}"
                                class="form-input">
                            <label class="text-gray-700 dark:text-darkmodetext" for="annually_setup">
                                {{ __('Annually Setup Fee') }}
                            </label>
                            <input type="text" name="annually_setup" id="annually_setup_fee"
                                value="{{ $pricing->annually_setup }}" class="form-input">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-700 dark:text-darkmodetext" for="biennially">
                                {{ __('Biennially') }}
                            </label>
                            <input type="text" name="biennially" id="biennially"
                                value="{{ $pricing->biennially }}" class="form-input">
                            <label class="text-gray-700 dark:text-darkmodetext" for="biennially_setup">
                                {{ __('Biennially Setup Fee') }}
                            </label>
                            <input type="text" name="biennially_setup" id="biennially_setup_fee"
                                value="{{ $pricing->biennially_setup }}" class="form-input">
                        </div>
                        <div class="flex flex-col">
                            <label class="text-gray-700 dark:text-darkmodetext" for="triennially">
                                {{ __('Triennially') }}
                            </label>
                            <input type="text" name="triennially" id="triennially"
                                value="{{ $pricing->triennially }}" class="form-input">
                            <label class="text-gray-700 dark:text-darkmodetext" for="triennially_setup">
                                {{ __('Triennially Setup Fee') }}
                            </label>
                            <input type="text" name="triennially_setup" id="triennially_setup_fee"
                                value="{{ $pricing->triennially_setup }}" class="form-input">
                        </div>
                    </div>
                @endif
                <!-- Quantity -->
                <div class="flex flex-row text-sm mt-4">
                    <div class="flex flex-col">
                        <label class="text-gray-700 dark:text-darkmodetext" for="allow_quantity">
                            {{ __('Allow multiple quantities') }}
                        </label>
                        <select name="allow_quantity" id="allow_quantity" class="form-input">
                            <option value="0" @if ($product->allow_quantity == 0) selected @endif>
                                {{ __('No') }}
                            </option>
                            <option value="1" @if ($product->allow_quantity == 1) selected @endif>
                                {{ __('Yes, Multiple Services (Each represents a own individual service instance)') }}
                            </option>
                            <option value="2" @if ($product->allow_quantity == 2) selected @endif>
                                {{ __('Yes, Single Service (One service instance with multiple quantity)') }}
                            </option>
                        </select>
                    </div>
                </div>
                <!-- Limit -->
                <div class="flex flex-row text-sm mt-4">
                    <div class="flex flex-col">
                        <label class="text-gray-700 dark:text-darkmodetext" for="limit">
                            {{ __('Limit per client') }}
                        </label>
                        <input type="text" name="limit" id="limit" value="{{ $product->limit }}"
                            class="form-input">
                    </div>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <button type="submit"
                        class="inline-flex justify-center w-max float-right button button-primary">
                        {{ __('Save') }}
                    </button>
                </div>
            </div>
        </div>
    </form>


</x-admin-layout>
