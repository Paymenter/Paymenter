<x-admin-layout>
    <x-slot name="title">
        {{ __('Order: ') }} {{ $order->id }}
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-md dark:shadow-gray-700 dark:bg-darkmode2 sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-darkmode2 sm:px-20">
                    <div class="mt-8 text-2xl dark:text-darkmodetext">
                        {{ __('Order: ') }} {{ $order->id }}
                    </div>
                    <div class="mt-6 text-gray-500 dark:text-darkmodetext">
                        <div class="flex flex-col">
                            <div class="flex flex-row justify-between">
                                <div class="flex flex-col">
                                    <span class="font-bold">{{ __('Total') }}:</span>
                                    <span>{{ $order->total }}</span>
                                </div>
                            </div>
                            <div class="flex flex-row justify-between">
                                <div class="flex flex-col">
                                    <span class="font-bold">{{ __('Created') }}:</span>
                                    <span>{{ $order->created_at }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold">{{ __('Updated') }}:</span>
                                    <span>{{ $order->updated_at }}</span>
                                </div>
                            </div>
                            <div class="flex flex-row justify-between">
                                <div class="flex flex-col">
                                    <span class="font-bold">{{ __('Status') }}:</span>
                                    <span>{{ $order->status }}</span>
                                </div>
                            </div>
                            <div class="flex flex-row justify-between">
                                <div class="flex flex-col">
                                    <span class="font-bold">{{ __('Next Due Date') }}:</span>
                                    <span>{{ $order->expiry_date }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col mt-4">
                            <span class="font-bold">{{ __('Products') }}:</span>
                            @if ($order->products->count())
                                @foreach ($products as $product)
                                    <span class="flex flex-row justify-between border-b dark:text-darkmodetext">
                                        <span>{{ $product->product()->get()->first()->name }}</span>
                                        <span>{{ $product->quantity }}</span>
                                        @if($product->link)
                                        <span
                                            class="hover:underline hover:text-blue-600 text-blue-800 dark:text-blue-400"><a
                                                href="{{ $product->link }}">{{ __('View') }}</a></span>
                                        @endif
                                    </span>
                                @endforeach
                            @else
                                <span>{{ __('No products found.') }}</span>
                            @endif
                        </div>
                    </div>
                    <!-- Buttons to edit/delete/suspend/unsuspend the user -->
                    <div class="flex flex-row justify-between mt-4">
                        <div class="flex flex-row">
                            @if ($order->status !== 'pending')
                                <form method="POST" action="{{ route('admin.orders.delete', $order->id) }}">
                                    @method('DELETE')
                                    @csrf
                                    <button class="form-submit bg-red-600">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            @elseif($order->status == 'pending')
                                <form method="POST" action="{{ route('admin.orders.paid', $order->id) }}">
                                    @csrf
                                    <button class="form-submit bg-green-600">
                                        {{ __('Mark as paid') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="flex
                                    flex-row">
                            @if ($order->status == 'paid')
                                <form method="POST" action="{{ route('admin.orders.suspend', $order->id) }}">
                                    @csrf
                                    <button class="mr-4 form-submit bg-red-600" type="submit">
                                        {{ __('Suspend') }}
                                    </button>
                                </form>
                            @elseif($order->status == 'suspended')
                                <form method="POST" action="{{ route('admin.orders.unsuspend', $order->id) }}">
                                    @csrf
                                    <button class="mr-4 form-submit bg-green-600" type="submit">
                                        {{ __('Unsuspend') }}
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.orders.create', $order->id) }}">
                                    @csrf
                                    <button class="mr-4 form-submit bg-green-600" type="submit">
                                        {{ __('Create') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
