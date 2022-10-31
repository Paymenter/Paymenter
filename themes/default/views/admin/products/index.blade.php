<x-admin-layout>
    <!-- show all products sorted on category -->
    <x-slot name="title">
        {{ __('Products') }}
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-darkmode2">
                <!-- right top aligned button -->
                <div class="flex justify-end pr-3 pt-3">
                    <a href="{{ route('admin.products.create') }}">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Create') }}
                        </button>
                    </a>
                </div>
                <x-success class="mb-4" />
                <div class="p-6 bg-white border-b border-gray-200 dark:bg-darkmode2">
                    <table class="min-w-full divide-y divide-gray-200" id="table">
                        @if ($categories->isEmpty())
                            <!-- not found -->
                            <div class="ml-10 flex items-baseline ">
                                <p class="text-gray-600 px-3 rounded-md text-xl m-4">
                                    {{ __('No products found') }}
                                </p>
                            </div>
                        @else
                            @foreach ($categories as $category)
                                @if ($category->products->isNotEmpty())
                                    <thead class="bg-gray-50 dark:bg-darkmode2">

                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $category->name }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $category->description }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-darkmode2 dark:text-darkmodetext">

                                        @foreach ($category->products()->get() as $product)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-darkmodetext">
                                                    {{ $product->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-darkmodetext">
                                                    {{ Str::limit($product->description, 50) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-darkmodetext">
                                                    <a href="{{ route('admin.products.edit', $product->id) }}">
                                                        <button
                                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                            {{ __('Edit') }}
                                                        </button>
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-darkmodetext">
                                                    <form method="POST"
                                                        action="{{ route('admin.products.destroy', $product->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @else
                                    <!-- not found -->
                                    <div class="ml-10 flex items-baseline ">
                                        <p class="text-gray-600 px-3 rounded-md text-xl m-4">
                                            {{ __('No products found on category') }} {{ $category->name }}
                                        </p>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- datatables -->
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.css" />

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
