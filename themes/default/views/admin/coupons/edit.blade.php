<x-admin-layout>
    <x-slot name="title">
        {{ __('Edit Coupon') }}
    </x-slot>

    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Edit Coupon') }}</h1>
    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 dark:text-darkmodetext">

            <div class="mt-4">
                <label for="code" class="block dark:text-darkmodetext">
                    {{ __('Code') }}
                </label>
                <input type="text" name="code" id="code" value="{{ $coupon->code }}"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                    required />
            </div>
            <div class="mt-4">
                <label for="type" class="block dark:text-darkmodetext">
                    {{ __('Type') }}
                </label>
                <select name="type" id="type"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                    required>
                    <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>
                        {{ __('Fixed') }}
                    </option>
                    <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>
                        {{ __('Percent') }}
                    </option>
                </select>
            </div>
            <div class="mt-4">
                <label for="time" class="block dark:text-darkmodetext">
                    {{ __('Time') }}
                </label>
                <select name="time" id="time"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                    required>
                    <option value="lifetime" {{ $coupon->time == 'lifetime' ? 'selected' : '' }}>
                        {{ __('Lifetime') }}
                    </option>
                    <option value="onetime" {{ $coupon->time == 'onetime' ? 'selected' : '' }}>
                        {{ __('One Time') }}
                    </option>
                </select>
            </div>
            <div class="mt-4">
                <label for="value" class="block dark:text-darkmodetext">
                    {{ __('Value') }} {{ config('settings::currency_sign') }}
                </label>
                <input name="value" id="value" value="{{ $coupon->value }}"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                    required />
            </div>
            <div class="mt-4">
                <label for="status" class="block dark:text-darkmodetext">
                    {{ __('Status') }}
                </label>
                <select name="status" id="status"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                    required>
                    <option value="active" {{ $coupon->status == 'active' ? 'selected' : '' }}>
                        {{ __('Active') }}
                    </option>
                    <option value="inactive" {{ $coupon->status == 'inactive' ? 'selected' : '' }}>
                        {{ __('Inactive') }}
                    </option>
                </select>
            </div>
            <div class="mt-4">
                <label for="start_date" class="block dark:text-darkmodetext">
                    {{ __('Start Date') }}
                </label>
                <input type="date" name="start_date" id="start_date" value="{{ $coupon->start_date }}"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md" />
            </div>
            <div class="mt-4">
                <label for="end_date" class="block dark:text-darkmodetext">
                    {{ __('End Date') }}
                </label>
                <input type="date" name="end_date" id="end_date" value="{{ $coupon->end_date }}"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md" />
            </div>
            <div class="w-full">
                <label class="block dark:text-darkmodetext" for="uses">
                    {{ __('Max Uses (not required)') }}
                </label>
                <input
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                    type="number" name="max_uses" id="max_uses" step="1" min="0"
                    value="{{ old('max_uses') }}">
            </div>
            <div class="mt-4">
                <label for="products" class="block dark:text-darkmodetext">
                    {{ __('Assigned Products') }}

                </label>
                <select name="products[]" id="products" multiple
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}"
                            @isset($coupon->products)
                            {{ in_array($product->id, $coupon->products) ? 'selected' : '' }} @endisset>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mt-4">
                <label for="used_by" class="block dark:text-darkmodetext">
                    {{ __('Times Used') }}
                </label>

                <input type="text" disabled
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                    value="{{ $coupon->uses }}">
            </div>

        </div>
        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="inline-flex justify-center w-max float-right button button-primary">
                {{ __('Save') }}
            </button>
        </div>
    </form>
</x-admin-layout>
