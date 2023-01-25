<x-admin-layout>
    <x-slot name="title">
        {{ __('Orders') }}
    </x-slot>
    <div class="dark:bg-darkmode dark:text-darkmodetext py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="dark:bg-darkmode2 overflow-hidden bg-white rounded-lg">
                <div class="dark:bg-darkmode2 p-6 bg-white">
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
                                <th>{{ __('Status') }}</th>
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
                                    <td>{{ $order->total }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>{{ $order->created_at }}</td>
                                    <td>{{ $order->updated_at }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="form-submit">
                                            {{ __('Show') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <style type="text/tailwindcss">
                table.dataTable {
            @apply text-gray-500 dark:text-gray-400 mb-4;
        }

        table.dataTable thead {
            @apply bg-gray-100 text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400;
        }

        table.dataTable thead th {
            @apply font-medium py-3 px-6;
        }

        table.dataTable tbody tr.odd {
            @apply bg-gray-50 dark:bg-gray-600;
        }

        table.dataTable tbody tr td {
            @apply bg-white border-b dark:bg-gray-800 dark:border-gray-700 py-4 px-6;
        }

        table.dataTable tfoot {
            @apply bg-gray-100 text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400;
        }

        table.dataTable tfoot th {
            @apply font-medium py-3 px-6;
        }

        .dataTables_filter {
            @apply p-2 bg-white dark:bg-gray-900 float-right;
        }

        .dataTables_filter label{
            @apply flex items-center space-x-4 text-gray-900 dark:text-white;
        }

        .dataTables_filter input {
            @apply ml-2 block p-2 w-80 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500;
        }

        .dataTables_info{
            @apply p-2 bg-white dark:bg-gray-900 float-left text-gray-500 dark:text-gray-400;
        }

        .dataTables_paginate {
            @apply p-2 bg-white dark:bg-gray-900 float-right text-gray-500 dark:text-gray-400 pt-3;
        }
        
        .dataTables_paginate span a {
			@apply py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white;
        }
        
        .dataTables_paginate span a.current {
			@apply z-10 py-2 px-3 leading-tight text-blue-600 bg-blue-50 border border-blue-300 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white;
        }      
        
		.dataTables_paginate .next {
         	@apply py-2 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white; 
		}
        
        .dataTables_paginate .previous {
			@apply py-2 px-3 ml-0 leading-tight text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white;
		}

        .dataTables_wrapper {
            @apply relative;
        }
    </style>

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
            $('.dt-button').addClass('dark:text-darkmodetext');
            $('.dataTables_filter label').addClass('dark:text-darkmodetext');
        });
    </script>
</x-admin-layout>
