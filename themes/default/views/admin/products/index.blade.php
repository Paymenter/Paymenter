<x-admin-layout title="Products">
    <div class="mt-8 text-2xl dark:text-darkmodetext text-center">
        {{ __('Products') }}
    </div>
    <!-- right top aligned button -->
    <div class="flex justify-end pr-3 pt-3">
        <a href="{{ route('admin.products.create') }}">
            <button class="button button-primary">
                {{ __('Create') }}
            </button>
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    @if ($categories->isEmpty())
        <!-- not found -->
        <div class="ml-10 flex items-baseline ">
            <p class="text-gray-600 px-3 rounded-md text-xl m-4">
                {{ __('No products found') }}
            </p>
        </div>
    @else
        <div id="categories">
            @foreach ($categories as $category)
                <div class="mt-10">
                    <h2 class="font-semibold text-2xl mb-2 text-secondary-900">{{ $category->name }}</h2>
                    <p class="ml-2">{{ $category->description }}</p>
                    <table class="min-w-full mt-4">
                        <thead class="bg-gray-50 dark:bg-secondary-200 text-left">
                            <tr>
                                <th class="px-1 pl-3 py-3">
                                    {{ __('Name') }}
                                </th>
                                <th class="py-3">
                                    {{ __('Description') }}
                                </th>
                                <th class="py-3">
                                    {{ __('Actions') }}
                                </th>
                                <th class="px-1 pr-2 py-3">
                                    {{ __('Order') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="" id="{{ $category->id }}">
                            @if ($category->products->isNotEmpty())
                                @foreach ($category->products()->orderBy('order')->get() as $product)
                                    <tr id="{{ $product->id }}">
                                        <td class="py-2 px-4">
                                            {{ $product->name }}</td>
                                        <td class="py-2 px-4">
                                            {{ Str::limit($product->description, 50) }}</td>
                                        <td class="py-2">
                                            <a href="{{ route('admin.products.edit', $product->id) }}">
                                                <button class="button button-primary">
                                                    {{ __('Edit') }}
                                                </button>
                                            </a>
                                        </td>
                                        <td class="w-10 text-2xl ml-4 font-thin text-center draggable">
                                            <i class="ri-drag-move-2-line draggable"></i>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <p class="text-gray-500 px-3 rounded-md text-xl m-4">
                                            {{ __('No products found on category') }} {{ $category->name }}
                                        </p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <script>
                        var el = document.getElementById('{{ $category->id }}');
                        var sortable = Sortable.create(el, {
                            animation: 150,
                            ghostClass: 'bg-gray-100',
                            chosenClass: 'bg-secondary-200',
                            handle: '.draggable',
                            onEnd: function(evt) {
                                var url = "{{ route('admin.products.reorder') }}";
                                var data = {
                                    id: evt.item.id,
                                    category_id: evt.item.parentNode.id,
                                    newIndex: evt.newIndex - 1,
                                    _token: '{{ csrf_token() }}'
                                };
                                // Plain JavaScript
                                var request = new XMLHttpRequest();
                                request.open('POST', url, true);
                                request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
                                request.send(JSON.stringify(data));

                                request.onload = function() {
                                    if (request.status >= 200 && request.status < 400) {
                                        // Success!
                                        var resp = request.responseText;
                                        console.log(resp);
                                    } else {
                                        // We reached our target server, but it returned an error
                                        console.log('error');
                                    }
                                };

                            },
                        });
                    </script>
                </div>
            @endforeach
        </div>
        {{-- <script>
            var el = document.getElementById('categories');
            var sortable = Sortable.create(el, {
                animation: 150,
                ghostClass: 'bg-gray-100',
                chosenClass: 'bg-secondary-200',
                handle: '.draggable',
                onEnd: function(evt) {
                    console.log(evt.oldIndex);
                },
            });
        </script> --}}
    @endif

</x-admin-layout>
