<x-admin-layout>
    <x-slot name="title">
        {{ __('Invoices') }}
    </x-slot>
    <h1 class="text-center text-2xl font-bold">{{ __('Invoices') }}</h1>
    <!-- Show single invoice -->
    <div class="mt-6 text-gray-500 dark:text-darkmodetext">
        <div class="flex flex-col">
            <div class="flex flex-row justify-between">
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Total') }}:</span>
                    <span>{{ config('settings::currency_sign') }}{{ $invoice->total() }}</span>
                </div>
            </div>
            <div class="flex flex-row justify-between">
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Created') }}:</span>
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
                    <span>{{ $invoice->status }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Client') }}:</span>
                    <a href="{{ route('admin.clients.edit', $invoice->user()->get()->first()->id) }}">
                        {{ $invoice->user()->get()->first()->name }}
                    </a>
                </div>
            </div>
            <div class="flex flex-row justify-between">
                <div class="flex flex-col">
                    <span class="font-bold">{{ __('Mark as paid') }}:</span>
                    <span>
                        <form action="{{ route('admin.invoices.paid', $invoice->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="form-submit">
                                {{ __('Mark as paid') }}
                            </button>
                        </form>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- Items -->
    <table class="table-auto w-full mt-6" id="table">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Discount')}} </th>
                <th>{{ __('Assigned Order') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ config('settings::currency_sign') }}{{ $item->price }}</td>
                    <td>{{ config('settings::currency_sign') }}{{ $item->discount }}</td>
                    <td>
                        @isset($item->order)
                            <a href="{{ route('admin.orders.show', $item->order->id) }}">
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
