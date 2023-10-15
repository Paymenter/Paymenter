<x-admin-layout>
    <x-slot name="title">
        {{ __('Tickets') }}
    </x-slot>
    <h1 class="text-2xl font-bold dark:text-darkmodetext text-center">{{ __('Tickets') }}</h1>
    <div class="flex items-center justify-end mt-4">
        <a href="{{ route('admin.tickets.create') }}" class="mr-4 button button-primary">
            {{ __('Create') }}
        </a>
    </div>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js">
    </script>
    <div class="overflow-x-auto">
        @if ($tickets->count())
            <table class="table-auto w-full" id="ticketsOpen">
                <thead>
                    <tr>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $service)
                        <tr onclick="window.location='{{ route('admin.tickets.show', $service->id) }}';" class="cursor-pointer">
                            <td class="text-clip overflow-hidden">
                                {{ $service->title }}
                            </td>
                            <td>
                                @if ($service->priority == 'low')
                                    <span
                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-600 text-green-800">
                                        {{ __('Low') }}
                                    </span>
                                @elseif($service->priority == 'medium')
                                    <span
                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-600 text-yellow-800">
                                        {{ __('medium') }}
                                    </span>
                                @elseif($service->priority == 'high')
                                    <span
                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-600 text-red-800">
                                        {{ __('High') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ $service->user->name }}
                            </td>
                            <td>
                                {{ $service->status }}
                            </td>
                            <td>
                                {{ $service->created_at }}
                            </td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $service->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <script>
                $(document).ready(function() {
                    var ticketsOpen = $('#ticketsOpen').DataTable({
                        dom: 'Bfrtip',
                        "language": {
                            "search": "{{ __('Search') }}",
                            "zeroRecords": "{{ __('No matching records found') }}",
                            "info": "{{ __('Showing') }} _PAGE_ {{ __('of') }} _PAGES_",
                            "infoEmpty": "{{ __('No records available') }}",
                            "infoFiltered": "{{ __('filtered from') }} _MAX_ {{ __('total records') }}",
                        },
                        order: [
                            [0, 'desc']
                        ],
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                    });
                });
            </script>
        @else
            <h3 class="dark:text-darkmodetext text-center my-4 text-xl"> No Open Tickets </h3>
        @endif
        @if ($closed->count())
            <h3 class="dark:text-darkmodetext text-center my-4 text-xl"> Closed Tickets </h3>
            <table class="table-auto w-full" id="ticketsClosed">
                <thead>
                    <tr>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="dark:bg-darkmode bg-white divide-y divide-gray-200">
                    @foreach ($closed as $service)
                        <tr onclick="window.location='{{ route('admin.tickets.show', $service->id) }}';" class="cursor-pointer">
                            <td class="text-clip overflow-hidden">
                                <div class="dark:text-darkmodetext text-sm text-gray-900">
                                    {{ $service->title }}
                                </div>
                            </td>
                            <td>
                                @if ($service->priority == 'low')
                                    <span
                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-600 text-green-800">
                                        {{ __('Low') }}
                                    </span>
                                @elseif($service->priority == 'medium')
                                    <span
                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-600 text-yellow-800">
                                        {{ __('medium') }}
                                    </span>
                                @elseif($service->priority == 'high')
                                    <span
                                        class="dark:text-darkmodetext px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-600 text-red-800">
                                        {{ __('High') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ $service->user->name }}
                            </td>
                            <td>
                                {{ $service->status }}
                            </td>
                            <td>
                                {{ $service->created_at }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.tickets.show', $service->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <script>
                $(document).ready(function() {
                    var ticketsClosed = $('#ticketsClosed').DataTable({
                        dom: 'Bfrtip',
                        "language": {
                            "search": "{{ __('Search') }}",
                            "zeroRecords": "{{ __('No matching records found') }}",
                            "info": "{{ __('Showing') }} _PAGE_ {{ __('of') }} _PAGES_",
                            "infoEmpty": "{{ __('No records available') }}",
                            "infoFiltered": "{{ __('filtered from') }} _MAX_ {{ __('total records') }}",
                        },
                        order: [
                            [0, 'desc']
                        ],
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ],
                    });
                });
            </script>
        @else
            <h3 class="dark:text-darkmodetext text-center my-4 text-xl"> No Closed Tickets </h3>
        @endif
    </div>
    </div>
    </div>
    </div>
    </div>
</x-admin-layout>
