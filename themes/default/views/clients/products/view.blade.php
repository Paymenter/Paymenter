<x-app-layout clients title="{{ __('Product') }}">
    <x-slot name="title">
        {{ __('Product: ') }} {{ $product->name }}
    </x-slot>
    <div class="content">
        <div class="content-box">
            <div class="mb-4">
                <h1 class="text-2xl font-bold">{{ __('Product: ') }}{{ $product->name }}</h1>
            </div>
            <div class="flex flex-wrap -mx-2 mb-4">
                <div class="w-full md:w-1/2 px-2">
                    <div class="mb-4">
                        <h2 class="text-lg font-bold">{{ __('Product Details') }}</h2>
                    </div>
                    <div class="mb-4">
                        <p><span class="font-bold">{{ __('Product Name') }}: </span>{{ $product->name }}</p>
                        <p><span class="font-bold">{{ __('Product Description') }}: </span>{{ $product->description }}
                        </p>
                        <p><span
                                class="font-bold">{{ __('Product Price') }}: </span>{{ config('settings::currency_sign') }}{{ $orderProduct->price }}
                        </p>
                    </div>
                </div>
                @if ($orderProduct->status == 'paid' && $orderProduct->expiry_date)
                    <div class="w-full md:w-1/2 px-2">
                        <div class="mb-4">
                            <h2 class="text-lg font-bold">{{ __('Status') }}</h2>
                        </div>
                        <div class="mb-4">
                            <p><span class="font-bold">{{ __('Due Date') }}: </span>{{ $orderProduct->expiry_date }}
                            </p>
                            <p><span
                                    class="font-bold">{{ __('Status') }}: </span>{{ $orderProduct->status == 'paid' ? 'Active' : 'Inactive' }}
                            </p>
                            <p> <span class="font-bold">
                                    {{ __('Billing Cycle') }}: {{ ucfirst($orderProduct->billing_cycle) }}
                                </span></p>
                        </div>
                    </div>
                @endif
            </div>
            <div class="sm:flex">
                @if ($link)
                    <div class="sm:flex-1">
                        <a href="{{ $link }}" class="button button-primary" target="_blank">
                            {{ __('Login to Product') }}
                        </a>
                    </div>
                @endif
                @if($orderProduct->getOpenInvoices()->count() > 0)
                    <div>
                        <a href="{{ route('clients.invoice.show', $orderProduct->getOpenInvoices()->first()->id) }}"
                            class="button button-primary">
                            {{ __('View open Invoice') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
