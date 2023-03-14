<x-admin-layout>
    <!-- show all products sorted on category -->
    <x-slot name="title">
        {{ __('Products') }}
    </x-slot>

    <div class="mt-8 text-2xl dark:text-darkmodetext text-center">
        {{ __('Products') }}
    </div>
    <!-- right top aligned button -->
    <div class="flex justify-end pr-3 pt-3">
        <a href="{{ route('admin.products.create') }}">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Create') }}
            </button>
        </a>
    </div>

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
                    <table class="min-w-full divide-y divide-gray-200" id="{{ $category->id }}">

                        <thead class="bg-gray-50 dark:bg-darkmode2">
                            <tr>
                                <th>
                                    {{ $category->name }}</th>
                                <th>
                                    {{ $category->description }}</th>
                                <th>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="">
                            @foreach ($category->products()->get() as $product)
                                <tr>
                                    <td>
                                        {{ $product->name }}</td>
                                    <td>
                                        {{ Str::limit($product->description, 50) }}</td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $product->id) }}">
                                            <button class="form-submit">
                                                {{ __('Edit') }}
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <script>
                        $(document).ready(function() {
                            var table = $('#{{ $category->id }}').DataTable({
                                dom: 'Bfrtip',
                                buttons: [
                                    'copy', 'csv', 'excel', 'pdf', 'print'
                                ]
                            });
                        });
                    </script>
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-colvis-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js">
    </script>
</x-admin-layout>
