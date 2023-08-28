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
                        <p><span class="font-bold">{{ __('Product Price') }}:
                            </span>{{ config('settings::currency_sign') }}{{ $orderProduct->price }}
                        </p>
                    </div>
                </div>
                @if ($orderProduct->expiry_date)
                    <div class="w-full md:w-1/2 px-2">
                        <div class="mb-4">
                            <h2 class="text-lg font-bold">{{ __('Status') }}</h2>
                        </div>
                        <div class="mb-4">
                            <p><span class="font-bold">{{ __('Due Date') }}: </span>{{ $orderProduct->expiry_date }}
                            </p>
                            <p><span class="font-bold">{{ __('Status') }}:
                                </span>{{ $orderProduct->status == 'paid' ? __('Active') : ucfirst($orderProduct->status) }}
                            </p>
                            <p>
                                <span class="font-bold">
                                    {{ __('Billing Cycle') }}:
                                </span>
                                {{ ucfirst($orderProduct->billing_cycle) }}
                            </p>
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
                @if ($orderProduct->getOpenInvoices()->count() > 0)
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
    <div class="content">
        @isset($views['pages'])
            <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                <li class="mr-2">
                    <a href="{{ route('clients.active-products.show', [$orderProduct->id]) }}"
                        class="inline-block p-4 text-blue-600 rounded-lg active dark:text-blue-500 border @if (request()->url() === route('clients.active-products.show', $orderProduct->id)) border-primary-400 @else border-transparent @endif hover:border-primary-400 hover:text-primary-400">
                        {{ __('Overview') }}
                    </a>
                </li>
                @foreach ($views['pages'] as $page)
                    <li class="mr-2">
                        <a href="{{ route('clients.active-products.show', [$orderProduct->id]) }}/{{ $page['url'] }}"
                            class="inline-block p-4 text-blue-600 rounded-lg active dark:text-blue-500 border @if (str_contains(request()->url(), $page['url'])) border-primary-400 @else border-transparent @endif hover:border-primary-400 hover:text-primary-400">
                            {{ $page['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endisset
        @isset($extensionLink)
            @isset($views['pages'])
                @foreach ($views['pages'] as $page)
                    @if ($extensionLink == $page['url'])
                        <div class="content-box">
                            @include($page['template'], $views['data'])
                        </div>
                    @endif
                @endforeach
            @endisset
        @else
            @isset($views['template'])
                <div class="content-box">
                    @include($views['template'], $views['data'])
                </div>
            @endisset
        @endisset
    </div>
</x-app-layout>
