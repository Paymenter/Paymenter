<x-admin-layout>
    <x-slot name="title">
        {{ __('Invoices') }}
    </x-slot>
    <h1 class="text-center text-2xl font-bold">{{ __('Invoices') }}</h1>
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
                    <td>{{ $invoice->user()->get()->first()->name }}</td>
                    <td>{{ $invoice->total() }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->created_at }}</td>
                    <td>{{ $invoice->updated_at }}</td>
                    <td>
                        <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="form-submit">
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
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
</x-admin-layout>
