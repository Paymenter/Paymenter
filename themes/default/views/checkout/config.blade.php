<x-app-layout>
    <x-slot name="title">
        {{ __('Checkout') }}
    </x-slot>
    <!-- form to configure the product -->
    <x-success class="m-2 mb-4" />

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2 dark:shadow-gray-700">
                <div class="p-6 bg-white sm:px-20 dark:bg-darkmode2">
                    <!-- Show product name and description -->
                    <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
                    <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600">
                    <div class="prose dark:prose-invert text-gray-500 dark:text-darkmodetext">
                        {{ \Illuminate\Mail\Markdown::parse(str_replace("\n", '<br>', $product->description)) }}
                    </div>
                    <form method="POST" action="{{ route('checkout.config', $product->id) }}">
                        @csrf
                        <div
                            class="items-center px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-darkmodetext">
                            @foreach ($userConfig as $config)
                                @if ($config->type == 'text')
                                    <div class="flex flex-col w-full mt-4">
                                        <label class="font-medium"
                                            for="{{ $config->name }}">{{ ucfirst($config->name) }}</label>
                                        <input
                                            class="block w-full rounded-md shadow-sm focus:ring-logo focus:border-logo sm:text-sm dark:bg-darkmode"
                                            id="{{ $config->name }}" type="text" name="{{ $config->name }}"
                                            value="{{ old($config->name) }}" required />
                                    </div>
                                @elseif($config->type == 'textarea')
                                    <div class="flex flex-col w-full mt-4">
                                        <label class="font-medium"
                                            for="{{ $config->name }}">{{ ucfirst($config->name) }}</label>
                                        <textarea class="block w-full rounded-md shadow-sm focus:ring-logo focus:border-logo sm:text-sm dark:bg-darkmode"
                                            id="{{ $config->name }}" name="{{ $config->name }}" required>{{ old($config->name) }}</textarea>
                                    </div>
                                @elseif($config->type == 'dropdown')
                                    <div class="flex flex-col w-full mt-4">
                                        <label class="font-medium"
                                            for="{{ $config->name }}">{{ ucfirst($config->name) }}</label>
                                        <select id="{{ $config->name }}" name="{{ $config->name }}"
                                            class="block w-full rounded-md shadow-sm focus:ring-logo focus:border-logo sm:text-sm dark:bg-darkmode">
                                            @foreach ($config->options as $option)
                                                <option value="{{ $option->value }}"
                                                    @if (old($config->name) == $option) selected @endif>
                                                    {{ $option->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @endforeach
                            @if ($prices->type == 'recurring')
                                <div class="flex flex-col w-full mt-4">
                                    <label class="font-medium" for="billing_cycle">{{ __('Billing cycle') }}</label>
                                    <select id="billing_cycle" name="billing_cycle"
                                        class="block w-full rounded-md shadow-sm focus:ring-logo focus:border-logo sm:text-sm dark:bg-darkmode">
                                        @if($prices->monthly)
                                            <option value="monthly" @if (old('billing_cycle') == 'monthly') selected @endif>
                                                {{ __('Monthly') . ' - ' . config('settings::currency_sign') . $prices->monthly }}
                                            </option>
                                        @endif
                                        @if($prices->quarterly)
                                            <option value="quarterly" @if (old('billing_cycle') == 'quarterly') selected @endif>
                                                {{ __('Quarterly') . ' - ' . config('settings::currency_sign') . $prices->quarterly }}
                                            </option>
                                        @endif
                                        @if($prices->semi_annually)
                                            <option value="semi_annually" @if (old('billing_cycle') == 'semi_annually') selected @endif>
                                                {{ __('Semi-annually') . ' - ' . config('settings::currency_sign') . $prices->semi_annually }}
                                            </option>
                                        @endif
                                        @if($prices->annually)
                                            <option value="annually" @if (old('billing_cycle') == 'annually') selected @endif>
                                                {{ __('Annually') . ' - ' . config('settings::currency_sign') . $prices->annually }}
                                            </option>
                                        @endif
                                        @if($prices->biennially)
                                            <option value="biennially" @if (old('billing_cycle') == 'biennially') selected @endif>
                                                {{ __('Biennially') . ' - ' . config('settings::currency_sign') . $prices->biennially }}
                                            </option>
                                        @endif
                                        @if($prices->triennially)
                                            <option value="triennially" @if (old('billing_cycle') == 'triennially') selected @endif>
                                                {{ __('Triennially') . ' - ' . config('settings::currency_sign') . $prices->triennially }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            @endif
                            <button type="submit" class="form-submit float-right my-2">
                                {{ __('Continue') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</x-app-layout>
