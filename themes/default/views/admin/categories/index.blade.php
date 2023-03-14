<x-admin-layout>
    <x-slot name="title">
        {{ __('Categories') }}
    </x-slot>
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Categories') }}</h1>
    <div class="flex justify-end pr-3 pt-3">
        <a href="{{ route('admin.categories.store') }}">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Create') }}
            </button>
        </a>
    </div>
    <div class="flex flex-wrap">
        <div class="w-full">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600" id="categories">
                <thead class="bg-gray-50 dark:bg-darkmode2 ">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-darkmode2 divide-y divide-gray-200">
                    @foreach ($categories as $category)
                        <tr>
                            <td>
                                {{ $category->name }}</td>
                            <td><a href="{{ route('products', $category->slug) }}" target="_blank"
                                    class="underline hover:text-blue-500 decoration-blue-500">{{ $category->slug }}</a>
                            </td>
                            <td>
                                <div class="flex flew-wrap">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                        class="mr-4 bg-blue-500 hover:bg-blue-700 form-submit">
                                        {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('admin.categories.delete', $category->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 form-submit">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/r-2.3.0/rr-1.2.8/datatables.min.js">
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#categories').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true
            });
        });
    </script>
</x-admin-layout>
