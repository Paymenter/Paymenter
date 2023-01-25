<x-admin-layout>
    <x-slot name="title">
        {{ __('Clients') }}
    </x-slot>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Clients') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                <!-- all clients -->
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-darkmode2 sm:px-20 ">
                        <div class="mt-8 text-2xl dark:text-darkmodetext">
                            {{ __('All clients') }}
                        </div>
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext">
                            {{ __('Here you can see all your clients.') }}
                        </div>
                        <div class="flex justify-end mt-4 mr-4">
                            <a href="{{ route('admin.clients.create') }}"
                                class="px-4 py-2 font-bold text-white transition bg-blue-500 rounded delay-400 dark:text-darkmodetext dark:bg-darkbutton dark:hover:bg-logo hover:bg-blue-700">
                                {{ __('Create client') }}
                            </a>
                        </div>
                    </div>
                    <!-- align right a button to create a new client -->
                    <div class="grid grid-cols-1 bg-gray-200 bg-opacity-25 dark:text-darkmodetext dark:bg-darkmode2">
                        <div class="p-6">
                            <table id="clientdatatable" class="table-auto w-full">
                                <thead>
                                    <tr>
                                        <th>
                                            {{ __('ID') }}
                                        </th>
                                        <th>
                                            {{ __('Name') }}
                                        </th>
                                        <th>
                                            {{ __('Email') }}
                                        </th>
                                        <th>
                                            {{ __('Created At') }}
                                        </th>
                                        <th>
                                            {{ __('Edit') }}
                                        </th>
                                        <th>
                                            {{ __('Delete') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>
                                                {{ $user->id }}
                                            </td>
                                            <td>
                                                {{ $user->name }}
                                            </td>
                                            <td>
                                                {{ $user->email }}
                                            </td>
                                            <td>
                                                {{ $user->created_at }}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.clients.edit', $user->id) }}"
                                                    class="px-4 py-1 text-sm text-blue-600 bg-blue-200 rounded-full">{{ __('Edit/View') }}</a>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.clients.delete', $user->id) }}"
                                                    method="POST" class="delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-4 py-1 text-sm text-red-600 bg-red-200 rounded-full">{{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/r-2.3.0/rr-1.2.8/datatables.min.js">
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#clientdatatable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true
            });
            $('.dt-button').addClass('dark:text-darkmodetext');
            $('.dataTables_filter label').addClass('dark:text-darkmodetext');
        });

        var form = document.getElementsByClassName('delete');
        var confirmIt = function(e) {
            if (!confirm('Are you sure you want to delete this client?\nThis will remove all data!')) e
                .preventDefault();
        };
        for (var i = 0, l = form.length; i < l; i++) {
            form[i].addEventListener('submit', confirmIt, false);
        }
    </script>
</x-admin-layout>
