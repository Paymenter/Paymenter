<x-admin-layout>
    <!-- show all products sorted on category -->
    <x-slot name="title">
        {{ __('Products') }}
    </x-slot>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <!-- right top aligned button -->
                <div class="flex justify-end pr-3 pt-3">
                    <a href="{{ route('admin.products.create') }}">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Create') }}
                        </button>
                    </a>
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-wrap">
                        <table>
                            @if ($categories->isEmpty())
                                <!-- not found -->
                                <div class="ml-10 flex items-baseline ">
                                    <p class="text-gray-600 px-3 rounded-md text-xl m-4">
                                        {{ __('No products found') }}
                                    </p>
                                </div>
                            @else
                                @foreach ($categories as $category)
                                    <thead>
                                        <tr>
                                            <th>{{ $category }}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories->products() as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $product->price }}</td>
                                                <td>{{ $product->stock }}</td>
                                                <td>
                                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                                        class="text-blue-500">Edit</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
