<x-admin-layout>
    <x-slot name="title">
        {{ __('Orders') }}
    </x-slot>
    <h1 class="text-center text-2xl font-bold">{{ __('Orders') }}</h1>
    @if ($orders->count() < 1)
        <div class="dark:bg-darkmode px-4 py-5 sm:px-6">
            <h3 class="dark:text-darkmodetext text-lg leading-6 font-medium text-gray-900">
                {{ __('Orders') }}
            </h3>
            <p class="dark:text-darkmodetext mt-1 max-w-2xl text-sm text-gray-500">
                {{ __('Order not found!') }}
            </p>
        </div>
    @endif
    <table class="table-auto w-full" id="table">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('User') }}</th>
                <th>{{ __('Total') }}</th>
                <th>{{ __('Created At') }}</th>
                <th>{{ __('Updated At') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->client()->get()->first()->name }}</td>
                    <td>{{ $order->total() }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>{{ $order->updated_at }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="form-submit">
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
