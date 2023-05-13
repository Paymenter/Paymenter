<x-app-layout>
    <x-slot name="title">
        {{ __('Checkout') }}
    </x-slot>
    <!-- form to configure the product -->
    <x-success class="m-2 mb-4" />

    
    <div class="content">
        <div class="content-box max-w-3xl mx-auto">
            <h1 class="text-xl font-bold">{{ $product->name }}</h1>
            <p>
                {{ \Illuminate\Mail\Markdown::parse(str_replace("\n", '<br>', Stevebauman\Purify\Facades\Purify::clean($product->description))) }}
            </p>
            <hr class="my-2 border-secondary-300">
            <div>
                <form method="POST" action="{{ route('checkout.config', $product->id) }}">
                    @csrf
                    @foreach ($userConfig as $config)
                        @if ($config->type == 'text')
                            <x-input 
                                type="text"
                                placeholder="{{ ucfirst($config->name) }}" 
                                name="{{ $config->name }}" 
                                id="{{ $config->name }}" 
                                label="{{ ucfirst($config->name) }}"
                                required
                            />
                        @elseif($config->type == 'textarea')
                            <div class="mt-4">
                                <label for={{ $config->name }} class="text-sm text-secondary-600">{{ ucfirst($config->name) }}</label>
                                <textarea
                                    class="py-2 bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300 border-secondary-300 focus:border-secondary-400 focus:ring-primary-400"
                                    placeholder="{{ ucfirst($config->name) }}" 
                                    name="{{ $config->name }}" 
                                    id="{{ $config->name }}"
                                    required
                                >
                                    {{ old($config->name) }}
                                </textarea>
                            </div>
                        @elseif($config->type == 'dropdown')
                            <div class="mt-4">
                                <label for={{ $config->name }} class="text-sm text-secondary-600">{{ ucfirst($config->name) }}</label>
                                <select
                                    class="py-2 bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300 border-secondary-300 focus:border-secondary-400 focus:ring-primary-400"
                                    name="{{ $config->name }}" 
                                    id="{{ $config->name }}"
                                    required
                                >
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
                        <div class="mt-4">
                            <label for="billing_cycle" class="text-sm text-secondary-600">{{ __('Billing cycle') }}</label>
                            <select
                                class="py-2 bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300 border-secondary-300 focus:border-secondary-400 focus:ring-primary-400"
                                name="billing_cycle" 
                                id="billing_cycle"
                                required
                            >
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
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="button button-primary">
                            {{ __('Continue') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>