<x-admin-layout title="roles">
    <h1 class="text-center text-2xl font-bold">{{ __('Roles') }}</h1>
        <!-- right top aligned button -->
    <div class="flex justify-end pr-3 pt-3">
        <a href="{{ route('admin.roles.create') }}">
            <button class="button button-primary">
                {{ __('Create') }}
            </button>
        </a>
    </div>
    <table class="table-auto w-full" id="table">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="button button-secondary">
                            {{ __('Edit') }}
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
