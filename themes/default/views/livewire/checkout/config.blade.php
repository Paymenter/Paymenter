<div class="content">
    <x-success />
    <div class="grid grid-cols-12 gap-4">
        <div class="lg:col-span-8 col-span-12">
            <div class="content-box overflow-hidden">
                <h1 class="text-xl font-bold">{{ $product->name }}</h1>
                <p>
                    {{ \Illuminate\Mail\Markdown::parse(str_replace("\n", '<br>', Stevebauman\Purify\Facades\Purify::clean($product->description))) }}
                </p>
                <hr class="my-2 border-secondary-300">
                <div>
                    @if ($prices->type == 'recurring')
                        <div class="mt-4">
                            <h3>{{ __('Billing cycle') }}</h3>
                            <div class="flex flex-row flex-wrap mt-2 mb-4 gap-4">
                                @php $priceTypes = ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially']; @endphp
                                @foreach ($priceTypes as $priceType)
                                    @if ($prices->{$priceType})
                                        <button type="button"
                                            class="button button-secondary flex flex-col items-center p-4 px-5 ring-offset-primary-400 ring-primary-400 @if ($billing_cycle == $priceType) ring-2 @endif"
                                            wire:click="setBillingCycle('{{ $priceType }}')">
                                            <h3 class="text-lg">
                                                {{ ucfirst($priceType == 'semi_annually' ? 'semi annually' : $priceType) }}
                                            </h3>
                                            <x-money :amount="$prices->{$priceType}" /></h3>
                                            @if ($prices->{$priceType . '_setup'})
                                                <div class="text-sm text-secondary-600">{{ __('Setup fee') }}: <x-money
                                                        :amount="$prices->{$priceType . '_setup'}" /></div>
                                            @endif
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (count($customConfig) > 0)
                        <h1 class="text-xl font-bold mt-6">{{ __('Configurable Options') }}</h1>
                    @endif
                    @foreach ($userConfig as $uconfig)
                        <x-config-item :config="$uconfig"
                            wire:change="update('{{ $uconfig->name }}', $event.target.value, true)" />
                    @endforeach
                    @foreach ($customConfig as $cconfig)
                        @php
                            $configItems = $cconfig
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
                                            placeholder="{{ ucfirst($name) }}" value="{{ $config[$item->id] }}"
                                            wire:change="update({{ $item->id }}, $event.target.value)" required />
                                        <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()"
                                            type="button"
                                            class="bg-secondary-200 text-secondary-500 hover:text-secondary-700 hover:bg-secondary-300 h-full w-20 rounded-r cursor-pointer">
                                            <span class="m-auto text-2xl font-thin">+</span>
                                        </button>
                                        <div class="flex items-center ml-1">
                                            x {{ ucfirst($name) }}
                                            @if ($item->configurableOptionInputs->first()->configurableOptionInputPrice->{$billing_cycle})
                                                <x-money :amount="$item->configurableOptionInputs->first()
                                                    ->configurableOptionInputPrice->{$billing_cycle}" />
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
                                                        @if (old($item->name) == $option || $config[$item->id] == $option->id) checked @elseif(!$key) checked @endif
                                                        wire:change="update({{ $item->id }}, $event.target.value)">
                                                    <label class="ml-2  inline-flex items-center"
                                                        for="{{ $option->id }}">
                                                        {{ ucfirst($name) }}
                                                        @if ($option->configurableOptionInputPrice->{$billing_cycle})
                                                            -
                                                            <x-money :amount="$option->configurableOptionInputPrice
                                                                ->{$billing_cycle}" />
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($item->type == 'slider')
                                    <div class="mt-2 h-20">
                                        <label for="{{ $item->id }}"
                                            class="text-sm text-secondary-600">{{ ucfirst($name) }}</label>
                                        <div class="flex flex-row justify-evenly">
                                            @php
                                                $items = $item->configurableOptionInputs()->where('hidden', '!=', 1)->orderBy('order', 'asc')->get();
                                                $selectKey = array_search($config[$item->id], array_column($items->toArray(), 'id'));
                                            @endphp
                                            @foreach ($items as $key => $option)
                                                @php $name = explode('|', $option->name)[1] ?? $option->name; @endphp
                                                <div class="flex flex-col w-full gap-1">
                                                    <div class="flex flex-col overflow-visible relative w-full justify-center items-center bg-secondary-400
                                                        @if($key == 0) rounded-l-full @elseif($key == count($items->toArray()) - 1) rounded-r-full @endif"
                                                        @if($config[$item->id] == $option->id) style="background: linear-gradient(to left, var(--secondary-400) 50%, var(--primary-400) 50%);" @elseif($selectKey > $key) style="background: var(--primary-400);" @endif>
                                                        <div class="rounded-full h-6 w-6 cursor-pointer hover:bg-secondary-400 @if ($config[$item->id] != $option->id) bg-secondary-500 @else bg-white @endif"
                                                            wire:click="update({{ $item->id }}, {{ $option->id }})">
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col justify-center items-center w-full">
                                                        <div class="text-xs text-secondary-600">{{ ucfirst($name) }}
                                                        </div>
                                                        @if ($option->configurableOptionInputPrice->{$billing_cycle})
                                                            <div class="text-xs text-secondary-600">
                                                                <x-money :amount="$option->configurableOptionInputPrice
                                                                    ->{$billing_cycle}" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($item->type == 'select')
                                    <x-input type="{{ $item->type }}" placeholder="{{ ucfirst($name) }}"
                                        name="{{ $item->id }}" id="{{ $item->id }}"
                                        label="{{ ucfirst($name) }}" required
                                        wire:change="update({{ $item->id }}, $event.target.value)">
                                        @foreach ($item->configurableOptionInputs()->orderBy('order', 'asc')->get() as $option)
                                            @if ($option->hidden)
                                                @continue
                                            @endif
                                            @php $name = explode('|', $option->name)[1] ?? $option->name; @endphp
                                            <option value="{{ $option->id }}"
                                                @if (old($item->name) == $option || $config[$item->id] == $option->id) selected @endif>
                                                {{ ucfirst($name) }}
                                                @if ($option->configurableOptionInputPrice->{$billing_cycle})
                                                    -
                                                    <x-money :amount="$option->configurableOptionInputPrice->{$billing_cycle}" />
                                                @endif
                                            </option>
                                        @endforeach
                                    </x-input>
                                @else
                                    <x-input type="{{ $item->type }}" placeholder="{{ ucfirst($name) }}"
                                        name="{{ $item->id }}" id="{{ $item->id }}"
                                        value="{{ old($item->name) ?? $config[$item->id] }}"
                                        label="{{ ucfirst($name) }}" required
                                        wire:change="update({{ $item->id }}, $event.target.value)">
                                    </x-input>
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
        <div class="lg:col-span-4 col-span-12">
            <div class="content-box">
                <h1 class="text-xl font-bold">{{ __('Order Summary') }}</h1>
                <div class="mt-2">
                    <div class="flex flex-row justify-between">
                        <div class="text-sm text-secondary-600">{{ $product->name }}</div>
                        <div class="text-sm text-secondary-600">
                            <x-money :amount="$prices->{$billing_cycle}" />
                        </div>
                    </div>
                    @foreach ($userConfig as $uconfig)
                        @php $uconfig->hidden = isset($uconfig->hidden) ? $uconfig->hidden : false; @endphp
                        @if ($uconfig->hidden)
                            @continue
                        @endif
                        <div class="flex flex-row justify-between">
                            <div class="text-sm text-secondary-600">{{ $uconfig->name }}</div>
                            <div class="text-sm text-secondary-600">
                                @php $key = array_search($uconfig->name, array_column($userConfig, 'name')); @endphp
                                @if ($uconfig->type == 'quantity')
                                    x {{ $userConfig[$key]['value'] ?? '' }}
                                @else
                                    {{ $userConfuserConfigigValues[$key]['value'] ?? '' }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @foreach ($customConfig as $cconfig)
                        @php
                            $configItems = $cconfig
                                ->configurableOptions()
                                ->where('hidden', false)
                                ->orderBy('order', 'asc')
                                ->get();
                        @endphp
                        @foreach ($configItems as $item)
                            <div class="flex flex-row justify-between ml-2">
                                <div class="text-sm text-secondary-600">
                                    @php $name = explode('|', $item->name)[1] ?? $item->name; @endphp
                                    {{ $name }}:
                                    @if ($config[$item->id] && $item->type !== 'text')
                                        @php $configName = explode('|', $item->configurableOptionInputs->where('id', $config[$item->id])->first()->name)[1] ?? $item->configurableOptionInputs->where('id', $config[$item->id])->first()->name; @endphp
                                        {{ $configName }}
                                    @elseif($config[$item->id])
                                        {{ $config[$item->id] }}
                                    @endif
                                </div>
                                <div class="text-sm text-secondary-600">
                                    @if ($item->type == 'quantity')
                                        x
                                        @if ($config[$item->id])
                                            <x-money :amount="$item->configurableOptionInputs
                                                ->where('id', $config[$item->id])
                                                ->first()->configurableOptionInputPrice->{$billing_cycle}" />
                                        @else
                                            <x-money :amount="$item->configurableOptionInputs->first()
                                                ->configurableOptionInputPrice->{$billing_cycle}" />
                                        @endif
                                    @else
                                        @if ($config[$item->id] && $item->type !== 'text')
                                            <x-money :amount="$item->configurableOptionInputs
                                                ->where('id', $config[$item->id])
                                                ->first()->configurableOptionInputPrice->{$billing_cycle}" />
                                        @else
                                            <x-money :amount="$item->configurableOptionInputs->first()
                                                ->configurableOptionInputPrice->{$billing_cycle}" />
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                    <hr class="my-2 border-secondary-300">
                    <div class="flex flex-row justify-between">
                        <div class="text-sm text-secondary-600">{{ __('Total') }}</div>
                        <div class="text-sm text-secondary-600">
                            <x-money :amount="$total" />
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" wire:click="checkout"
                        class="button button-primary w-full">{{ __('Continue to Checkout') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
