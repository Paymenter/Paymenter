<x-admin-layout>
    <x-slot name="title">
        {{ __('Invoices') }}
    </x-slot>
    <h1 class="text-center text-2xl font-bold">{{ __('Invoice') }} #{{ $invoice->id }}</h1>
    <!-- Show single invoice -->
    <div class="mt-6 text-gray-500 dark:text-darkmodetext">
        <div class="flex flex-col">
            <div class="flex flex-row justify-between">
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Total') }}:</span>
                    <span>{{ config('settings::currency_sign') }}{{ $total }}</span>
                </div>
            </div>
            <div class="flex flex-row justify-between">
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Invoice Date') }}:</span>
                    <span>{{ $invoice->created_at }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Updated') }}:</span>
                    <span>{{ $invoice->updated_at }}</span>
                </div>
            </div>
            <div class="flex flex-row justify-between">
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Status') }}:</span>
                    <span> {{ $invoice->status }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Client') }}:</span>
                    <a href="{{ route('admin.clients.edit', $invoice->user->id) }}"
                        class="text-blue-500 underline underline-offset-2">
                        {{ $invoice->user->name }}
                    </a>
                </div>
            </div>
            @if ($invoice->status !== 'paid')
                <div class="flex flex-row justify-between mt-3">
                    <div class="flex flex-col">
                        <span>
                            <button class="button button-primary" data-modal-target="{{ $invoice->id }}"
                                data-modal-toggle="{{ $invoice->id }}">
                                {{ __('Mark as paid') }}
                            </button>
                        </span>
                    </div>
                </div>
                <form action="{{ route('admin.invoices.paid', $invoice->id) }}" method="POST">
                <x-modal :id="$invoice->id" title="Marking invoice {{ $invoice->id }} as paid">
                        @csrf
                        <x-input type="select" name="paid_with" label="Payment Method">
                            @foreach (App\Models\Extension::where('type', 'gateway')->where('enabled', true)->get() as $extension)
                                <option value="{{ $extension->name }}">{{ $extension->name }}</option>
                            @endforeach
                            <option value="manual">{{ __('Manual') }}</option>
                        </x-input>

                        <x-input type="text" name="paid_reference" label="Reference" />

                        <x-slot name="footer">
                            <button class="button button-primary float-right"  type="submit">
                                {{ __('Mark as paid') }}
                            </button>
                        </x-slot>
                    </x-modal>
                </form>
            @else
                <div class="flex flex-row justify-between mt-3">
                    <div class="flex flex-col">
                        <span class="font-bold">{{ __('Paid At') }}:</span>
                        <span>{{ $invoice->paid_at }}</span>
                    </div>
                </div>
                <div class="flex flex-row justify-between mt-3">
                    <div class="flex flex-col">
                        <span class="font-bold">{{ __('Payment Method') }}:</span>
                        <span>{{ $invoice->paid_with }}</span>
                    </div>
                    @isset($invoice->paid_reference)
                        <div class="flex flex-col">
                            <span class="font-bold">{{ __('Reference') }}:</span>
                            <span>{{ $invoice->paid_reference }}</span>
                        </div>
                    @endisset
                </div>
            @endif

        </div>
    </div>
    <!-- Items -->
    <table class="table-auto w-full mt-6" id="table">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Discount') }} </th>
                <th>{{ __('Assigned Order') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ config('settings::currency_sign') }}{{ $item->price }}</td>
                    <td>{{ config('settings::currency_sign') }}{{ $item->discount }}</td>
                    <td>
                        @isset($item->order)
                            <a href="{{ route('admin.orders.show', $item->order->id) }}"
                                class="text-primary-400 underline underline-offset-2">
                                {{ $item->order->id }}
                            </a>
                        @endisset
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js">
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
</x-admin-layout>
