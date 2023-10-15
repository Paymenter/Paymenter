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
                        <x-config-item :config="$config" />
                    @endforeach
                    @if ($prices->type == 'recurring')
                        <div class="mt-4">
                            <label for="billing_cycle"
                                class="text-sm text-secondary-600">{{ __('Billing cycle') }}</label>
                            <select
                                class="py-2 bg-secondary-200 text-secondary-800 font-medium rounded-md placeholder-secondary-500 outline-none w-full border focus:ring-2 focus:ring-offset-2 ring-offset-secondary-50 dark:ring-offset-secondary-100 duration-300 border-secondary-300 focus:border-secondary-400 focus:ring-primary-400"
                                name="billing_cycle" id="billing_cycle" required>
                                @if ($prices->monthly)
                                    <option value="monthly" @if ($billing_cycle == 'monthly' || old('billing_cycle') == 'monthly') selected @endif>
                                        {{ __('Monthly') . ' - ' . config('settings::currency_sign') . $prices->monthly }}
                                    </option>
                                @endif
                                @if ($prices->quarterly)
                                    <option value="quarterly" @if ($billing_cycle == 'quarterly' || old('billing_cycle') == 'quarterly') selected @endif>
                                        {{ __('Quarterly') . ' - ' . config('settings::currency_sign') . $prices->quarterly }}
                                    </option>
                                @endif
                                @if ($prices->semi_annually)
                                    <option value="semi_annually" @if ($billing_cycle == 'semi_annually' || old('billing_cycle') == 'semi_annually') selected @endif>
                                        {{ __('Semi-annually') . ' - ' . config('settings::currency_sign') . $prices->semi_annually }}
                                    </option>
                                @endif
                                @if ($prices->annually)
                                    <option value="annually" @if ($billing_cycle == 'annually' || old('billing_cycle') == 'annually') selected @endif>
                                        {{ __('Annually') . ' - ' . config('settings::currency_sign') . $prices->annually }}
                                    </option>
                                @endif
                                @if ($prices->biennially)
                                    <option value="biennially" @if ($billing_cycle == 'biennially' || old('billing_cycle') == 'biennially') selected @endif>
                                        {{ __('Biennially') . ' - ' . config('settings::currency_sign') . $prices->biennially }}
                                    </option>
                                @endif
                                @if ($prices->triennially)
                                    <option value="triennially" @if ($billing_cycle == 'triennially' || old('billing_cycle') == 'triennially') selected @endif>
                                        {{ __('Triennially') . ' - ' . config('settings::currency_sign') . $prices->triennially }}
                                    </option>
                                @endif
                            </select>
                        </div>
                        <!-- Onchange of the billing cycle, reload the page with the new billing cycle -->
                        <script>
                            document.getElementById('billing_cycle').onchange = function() {
                                window.location.href = "{{ route('checkout.config', $product->id) }}?billing_cycle=" + this.value;
                            };
                        </script>
                    @endif
                    @if (count($customConfig) > 0)
                        <h1 class="text-xl font-bold mt-6">{{ __('Configurable Options') }}</h1>
                    @endif
                    @foreach ($customConfig as $config)
                        @php
                            $configItems = $config
                                ->configurableOptions()
                                ->orderBy('order', 'asc')
                                ->get();
                        @endphp
                        @foreach ($configItems as $item)
                            @if ($item->hidden)
                                @continue
                            @endif
                            <div class="mt-2">
                                @php $name = explode('|', $item->name)[1] ?? $item->name; @endphp
                                @if ($item->type == 'quantity')
                                    <!-- Display the quantity input with plus and minus buttons -->
                                    <div class="flex flex-row h-10 w-full rounded-lg relative bg-transparent mt-2">
                                        <button
                                            onclick="if (this.parentNode.querySelector('input[type=number]').value > 0) this.parentNode.querySelector('input[type=number]').stepDown()"
                                            type="button"
                                            class="bg-secondary-200 text-secondary-500 hover:text-secondary-700 hover:bg-secondary-300 h-full w-20 rounded-l cursor-pointer outline-none">
                                            <span class="m-auto text-2xl font-thin">−</span>
                                        </button>
                                        <x-input type="number" name="{{ $item->id }}" id="{{ $item->id }}"
                                            placeholder="{{ ucfirst($name) }}" value="0" min="0"
                                            required />
                                        <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()"
                                            type="button"
                                            class="bg-secondary-200 text-secondary-500 hover:text-secondary-700 hover:bg-secondary-300 h-full w-20 rounded-r cursor-pointer">
                                            <span class="m-auto text-2xl font-thin">+</span>
                                        </button>
                                        <div class="flex items-center ml-1">
                                            x {{ ucfirst($name) }}
                                            @if ($item->configurableOptionInputs->first()->configurableOptionInputPrice->{$billing_cycle})
                                                {{ config('settings::currency_sign') . $item->configurableOptionInputs->first()->configurableOptionInputPrice->{$billing_cycle} }}
                                            @else
                                                free
                                            @endif
                                        </div>
                                    </div>
                                @elseif($item->type == 'radio')
                                    <div class="mt-2">
                                        <label for="{{ $item->id }}"
                                            class="text-sm text-secondary-600">{{ ucfirst($name) }}</label>
                                        <div class="flex flex-col radios">
                                            @foreach ($item->configurableOptionInputs()->orderBy('order', 'asc')->get() as $key => $option)
                                                @php $name = explode('|', $option->name)[1] ?? $option->name; @endphp
                                                @if ($option->hidden)
                                                    @continue
                                                @endif
                                                <div class="inline-flex">
                                                    <input type="radio" id="{{ $option->id }}"
                                                        name="{{ $item->id }}" value="{{ $option->id }}"
                                                        @if (old($item->name) == $option) checked @elseif(!$key) checked @endif>
                                                    <label class="ml-2  inline-flex items-center"
                                                        for="{{ $option->id }}">
                                                        {{ ucfirst($name) }}
                                                        @if ($option->configurableOptionInputPrice->{$billing_cycle})
                                                            -
                                                            {{ config('settings::currency_sign') . $option->configurableOptionInputPrice->{$billing_cycle} }}
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($item->type == 'slider')
                                    <div class="mt-2">
                                        @php $includeJs = true; @endphp
                                        <label for="{{ $item->id }}"
                                            class="text-sm text-secondary-600">{{ ucfirst($name) }}</label>
                                        <div class="flex flex-col radios">
                                            @foreach ($item->configurableOptionInputs()->orderBy('order', 'asc')->get() as $key => $option)
                                                @php $name = explode('|', $option->name)[1] ?? $option->name; @endphp
                                                @if ($option->hidden)
                                                    @continue
                                                @endif
                                                <input type="radio" id="{{ $option->id }}"
                                                    name="{{ $item->id }}" value="{{ $option->id }}"
                                                    @if (old($item->name) == $option) checked @elseif(!$key) checked @endif>
                                                <label class="ml-2  inline-flex items-center"
                                                    for="{{ $option->id }}">
                                                    {{ ucfirst($name) }}
                                                    @if ($option->configurableOptionInputPrice->{$billing_cycle})
                                                        -
                                                        {{ config('settings::currency_sign') . $option->configurableOptionInputPrice->{$billing_cycle} }}
                                                    @endif
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($item->type == 'select')
                                    <x-input type="{{ $item->type }}" placeholder="{{ ucfirst($item->name) }}"
                                        name="{{ $item->id }}" id="{{ $item->id }}"
                                        label="{{ ucfirst($name) }}" required>
                                        @foreach ($item->configurableOptionInputs()->orderBy('order', 'asc')->get() as $option)
                                            @if ($option->hidden)
                                                @continue
                                            @endif
                                            @php $name = explode('|', $option->name)[1] ?? $option->name; @endphp
                                            <option value="{{ $option->id }}"
                                                @if (old($item->name) == $option) selected @endif>
                                                {{ ucfirst($name) }}
                                                @if ($option->configurableOptionInputPrice->{$billing_cycle})
                                                    -
                                                    {{ config('settings::currency_sign') . $option->configurableOptionInputPrice->{$billing_cycle} }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </x-input>
                                @else 
                                    <x-input type="{{ $item->type }}" placeholder="{{ ucfirst($item->name) }}"
                                        name="{{ $item->id }}" id="{{ $item->id }}"
                                        label="{{ ucfirst($name) }}" required>
                                    </x-input>
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="button button-primary">
                            {{ __('Continue') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @isset($includeJs)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/radioslider@1.0.0-beta.1/dist/radioslider.min.css">

        <script src="https://cdn.jsdelivr.net/npm/radioslider@1.0.0-beta.1/dist/jquery.radioslider.min.js"></script>
        <script>
            $(function() {
                var radios = $(".radios").radioslider();
            });
        </script>
    @endisset
</x-app-layout>
