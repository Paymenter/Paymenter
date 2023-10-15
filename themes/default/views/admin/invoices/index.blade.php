<x-admin-layout>
    <x-slot name="title">
        {{ __('Invoices') }}
    </x-slot>
    <div class="p-3 bg-white dark:bg-secondary-100 flex flex-row justify-between">
        <div>
            <div class="mt-3 text-2xl font-bold dark:text-darkmodetext">
                {{ __('Invoices') }}
            </div>
            <div class="mt-3 text-gray-500 dark:text-darkmodetext">
                {{ __('Here you can see all invoices.') }}
            </div>
        </div>
        <div class="flex my-auto float-end justify-end mr-4">
            <a href="{{ route('admin.invoices.create') }}" class="px-4 py-2 font-bold text-white transition rounded delay-400 button button-primary">
                <i class="ri-add-circle-line mt-2"></i> {{ __('Create') }}
            </a>
        </div>
    </div>
    <table class="table-auto w-full" id="table">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('User') }}</th>
                <th>{{ __('Total') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Created At') }}</th>
                <th>{{ __('Paid At') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->user->name }}</td>
                    <td>{{ $invoice->total() }} {{ config('settings::currency_sign') }}</td>
                    <td>
                        @if (ucfirst($invoice->status) == 'Pending')
                            <span class="text-red-400 font-semibold">
                                {{ __('Pending') }}
                            </span>
                        @elseif (ucfirst($invoice->status) == 'Paid')
                            <span class="text-green-400 font-semibold">
                                {{__('Paid')}}
                            </span>
                        @elseif (ucfirst($invoice->status) == 'Cancelled')
                            <span class="text-orange-400 font-semibold">
                                {{__('Cancelled')}}
                            </span>
                        @else
                            <span class="text-gray-400 font-semibold">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        @endif
                    </td>
                    <td>{{ $invoice->created_at }}</td>
                    <td>{{ $invoice->paid_at ?? 'Not Paid' }}</td>
                    <td>
                        <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="button button-primary">
                            {{ __('Show') }}
                        </a>
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
                "language": {
                    "search": "{{__('Search')}}",
                    "zeroRecords": "{{__('No matching records found')}}",
                    "info": "{{__('Showing')}} _PAGE_ {{__('of')}} _PAGES_",
                    "infoEmpty": "{{__('No records available')}}",
                    "infoFiltered": "{{__('filtered from')}} _MAX_ {{__('total records')}}",
                },
                order: [[ 0, 'desc' ]],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
        });
    </script>
</x-admin-layout>
