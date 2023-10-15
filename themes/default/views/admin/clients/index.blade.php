<x-admin-layout>
    <x-slot name="title">
        {{ __('Clients') }}
    </x-slot>
    <div class="p-3 bg-white dark:bg-secondary-100 flex flex-row justify-between">
        <div>
            <div class="mt-3 text-2xl font-bold dark:text-darkmodetext">
                {{ __('All clients') }}
            </div>
            <div class="mt-3 text-gray-500 dark:text-darkmodetext">
                {{ __('Here you can see all your clients.') }}
            </div>
        </div>
        <div class="flex my-auto float-end justify-end mr-4">
            <a href="{{ route('admin.clients.create') }}"
               class="px-4 py-2 font-bold text-white transition rounded delay-400 bg-blue-500 button button-primary">
                <i class="ri-user-add-line"></i> {{ __('Create client') }}
            </a>
        </div>
    </div>
    <div class="p-6">
        <!-- align right a button to create a new client -->
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
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            {{ $user->id }}
                        </td>
                        <td>
                            <div class="flex flex-row items-center">
                                <img class="w-8 h-8 rounded-md" src="https://www.gravatar.com/avatar/{{md5($user->email)}}?s=200&d=mp" alt="Avatar"/>
                                <span class="ml-2">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>
                            {{ $user->email }}
                        </td>
                        <td>
                            {{ $user->created_at }}
                        </td>
                        <td>
                            <a href="{{ route('admin.clients.edit', $user->id) }}"
                                class="form-submit bg-blue-500 button button-primary">{{ __('Edit/View') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
