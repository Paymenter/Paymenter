<x-admin-layout title="Configurable Options">
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Configurable Options') }}</h1>
    <div class="flex justify-end pr-3 pt-3">
        <a href="{{ route('admin.configurable-options.create') }}">
            <button class="button button-primary">
                {{ __('Create') }}
            </button>
        </a>
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
                    <th>
                        {{ __('Delete') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($configurableGroups as $configurableGroup)
                    <tr>
                        <td>
                            {{ $configurableGroup->id }}
                        </td>
                        <td>
                            {{ $configurableGroup->name }}
                        </td>
                        <td>
                            {{ $configurableGroup->description }}
                        </td>
                        <td>
                            {{ $configurableGroup->created_at }}
                        </td>
                        <td>
                            <a href="{{ route('admin.configurable-options.edit', $configurableGroup->id) }}"
                                class="button button-primary">{{ __('Edit/View') }}</a>
                        </td>
                        <td>
                            <form method="POST"
                                action="{{ route('admin.configurable-options.delete', $configurableGroup->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button button-danger">{{ __('Delete') }}</button>
                            </form>
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
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
            });
        });
    </script>
</x-admin-layout>