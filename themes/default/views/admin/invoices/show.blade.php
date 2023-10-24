<x-admin-layout>
    <x-slot name="title">
        {{ __('Invoices') }}
    </x-slot>
    <div class="flex flex-row justify-between w-full mb-2">
        <div class="text-2xl leading-5 mt-1 font-bold dark:text-darkmodetext">
            {{ __('Invoice') }} #{{ $invoice->id }}
        </div>
        <!-- TODO: BUTTONS FOR CANCEL, DELETE ETC. -->
        @if($invoice->status === 'pending')
            <div>
                <button class="button button-primary text-sm" data-modal-target="{{ $invoice->id }}" data-modal-toggle="{{ $invoice->id }}">
                    <i class="ri-money-dollar-circle-line"></i> {{ __('Mark as paid') }}
                </button>
                <form action="{{ route('admin.invoices.paid', $invoice->id) }}" method="POST">
                    <x-modal :id="$invoice->id" title="{{__('Marking invoice')}} {{ $invoice->id }} {{__('as paid')}}">
                        @csrf
                        <x-input type="select" name="paid_with" label="Payment Method">
                            @foreach (App\Models\Extension::where('type', 'gateway')->where('enabled', true)->get() as $extension)
                                <option value="{{ $extension->name }}">{{ $extension->name }}</option>
                            @endforeach
                            <option value="manual">{{ __('Manual') }}</option>
                        </x-input>

                        <x-input type="text" name="paid_reference" label="{{__('Reference')}}" />

                        <x-slot name="footer">
                            <button class="button button-primary float-right"  type="submit">
                                {{ __('Mark as paid') }}
                            </button>
                        </x-slot>
                    </x-modal>
                </form>
            </div>
        @endif
    </div>

    @php
        $invoice->paid_with = $invoice->paid_with=== "unknown"? __('Not Paid')  : $invoice->paid_with;
    @endphp

    <div class="grid grid-cols-1 gap-4 w-full">
        <div class="text-gray-500 dark:text-darkmodetext dark:bg-secondary-100">
            <div class="hidden lg:flex flex-row gap-x-4">
                <div class="flex flex-col items-baseline w-full">
                    <a class="hover:cursor-pointer hover:shadow-sm w-full" onclick="window.location.href='{{ route('admin.clients.edit', $invoice->user->id) }}'">
                        <x-input disabled type="text" name="client" :label="__('Client') . ' (' . __('Click to show').')'" name="title" value="{{ $invoice->user->name }}" class="mt-2 lg:mt-0" icon="ri-user-line" />
                    </a>
                    <div class="flex flex-row w-full gap-x-4">
                        <x-input disabled type="text" name="total" :label="__('Total')" icon="ri-money-dollar-circle-line" class="w-full mt-2 lg:mt-0" value="{{ ucfirst($total) }} {{ config('settings::currency_sign') }}"/>
                        <x-input disabled type="text" name="status" :label="__('Status')" icon="ri-calendar-line" class="w-full mt-2 lg:mt-0" value="{{ ucfirst($invoice->status) }}"/>
                    </div>
                </div>
                <div class="flex flex-col items-baseline w-full">
                    <div class="flex flex-row w-full gap-x-4">
                        <x-input disabled type="text" name="paid_with" :label="__('Payment Method')" icon="ri-money-dollar-circle-line" class="w-full mt-2 lg:mt-0" value="{{ ucfirst($invoice->paid_with) }}"/>
                        <x-input disabled type="text" name="reference" :label="__('Reference')" icon="ri-money-dollar-circle-line" class="w-full mt-2 lg:mt-0" value="{{ $invoice->reference??__('No Reference') }}"/>
                    </div>
                    <div class="flex flex-row gap-x-4">
                        <x-input disabled type="text" name="created_at" :label="__('Created At')" icon="ri-calendar-line" class="w-full mt-2 lg:mt-0" value="{{ $invoice->created_at }}"/>
                        <x-input disabled type="text" name="updated_at" :label="__('Updated At')" icon="ri-calendar-line" class="w-full mt-2 lg:mt-0" value="{{ $invoice->updated_at }}"/>
                        <x-input disabled type="text" name="paid_at" :label="__('Paid At')" icon="ri-calendar-line" class="w-full mt-2 lg:mt-0" value="{{ $invoice->paid_at?? __('Not Paid') }}"/>
                    </div>
                </div>
            </div>
            <div class="flex lg:hidden flex-row gap-x-4">
                <div class="flex flex-col items-baseline w-full">
                    <a class="hover:cursor-pointer hover:shadow-sm w-full" onclick="window.location.href='{{ route('admin.clients.edit', $invoice->user->id) }}'">
                        <x-input disabled type="text" name="client" :label="__('Client') . ' (' . __('Click to show').')'" name="title" value="{{ $invoice->user->name }}" class="mt-2 lg:mt-0" icon="ri-user-line" />
                    </a>

                    <x-input disabled type="text" name="total" :label="__('Total')" icon="ri-money-dollar-circle-line" class="w-full mt-2 lg:mt-0" value="{{ ucfirst($total) }} {{ config('settings::currency_sign') }}"/>

                    <x-input disabled type="text" name="status" :label="__('Status')" icon="ri-calendar-line" class="w-full mt-2 lg:mt-0" value="{{ ucfirst($invoice->status) }}"/>

                    @if($invoice->paid_with !== "unknown")
                        <x-input disabled type="text" name="paid_with" :label="__('Payment Method')" icon="ri-money-dollar-circle-line" class="w-full mt-2 lg:mt-0" value="{{ ucfirst($invoice->paid_with) }}"/>

                        <x-input disabled type="text" name="reference" :label="__('Reference')" icon="ri-money-dollar-circle-line" class="w-full mt-2 lg:mt-0" value="{{ $invoice->reference }}"/>
                    @endif

                    <x-input disabled type="text" name="paid_at" :label="__('Paid At')" icon="ri-calendar-line" class="w-full mt-2 lg:mt-0" value="{{ $invoice->paid_at?? __('Not Paid') }}"/>

                    <x-input disabled type="text" name="created_at" :label="__('Created At')" icon="ri-calendar-line" class="w-full mt-2 lg:mt-0" value="{{ $invoice->created_at }}"/>

                    <x-input disabled type="text" name="updated_at" :label="__('Updated At')" icon="ri-calendar-line" class="w-full mt-2 lg:mt-0" value="{{ $invoice->updated_at }}"/>
                </div>
            </div>
        </div>
    </div>

    <h3 class="text-xl mt-5 text-center leading-5 font-bold dark:text-darkmodetext">
        {{ __('Products') }}
    </h3>
    <div class='overflow-x-scroll lg:overflow-hidden'>
        <table class='mt-4 min-w-max gap-y-3 table-auto w-full'>
            <thead>
                <tr class="border-b-2 border-secondary-200">
                    <th class="text-center p-2">{{ __('ID') }}</th>
                    <th class="text-center p-2">{{ __('Name') }}</th>
                    <th class="text-center p-2">{{ __('Price') }}</th>
                    <th class="text-center p-2">{{ __('Discount') }} </th>
                    <th class="text-center p-2">{{ __('Assigned Order') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $item)
                    <tr>
                        <td class="text-center p-2">{{ $item->id }}</td>
                        <td class="text-center p-2">{{ $item->name }}</td>
                        <td class="text-center p-2">
                            <x-money :amount="$item->price" />
                        </td>
                        <td class="text-center p-2">
                            <x-money :amount="$item->discount" />
                        </td>
                        <td class="text-center p-2">
                            @if($item->order)
                                <a href="{{ route('admin.orders.show', $item->order->id) }}"
                                    class="text-primary-400 underline underline-offset-2">
                                    {{ $item->order->id }}
                                </a>
                            @else
                                {{ __('Not assigned') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-admin-layout>
