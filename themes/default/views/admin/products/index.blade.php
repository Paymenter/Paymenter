<x-admin-layout title="Products">
    <div class="p-3 bg-white dark:bg-secondary-100 flex flex-row justify-between">
        <div>
            <div class="mt-3 text-2xl font-bold dark:text-darkmodetext">
                {{ __('Products') }}
            </div>
            <div class="mt-3 text-gray-500 dark:text-darkmodetext">
                {{ __('Here you can see all products.') }}
            </div>
        </div>
        <div class="flex my-auto float-end justify-end mr-4">
            <a href="{{ route('admin.products.create') }}"
               class="px-4 py-2 font-bold text-white transition rounded delay-400 bg-blue-500 button button-primary">
                <i class="ri-user-add-line"></i> {{ __('Create') }}
            </a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    @if ($categories->isEmpty())
        <div class="ml-10 flex items-baseline ">
            <p class="text-gray-600 px-3 rounded-md text-xl m-4">
                {{ __('No products found') }}

            </p>
            <button class="button button-primary">
                <a href="{{ route('admin.categories.create') }}">
                    {{ __('Create category') }}
                </a>
            </button>
        </div>
    @else
        <div id="categories">
            @foreach ($categories as $category)
                <div class="mt-10">
                    <h2 class="text-2xl mb-2 text-secondary-900 flex-1 dark:text-darkmodetext"><b>{{__('Category')}}:</b> {{ $category->name }}</h2>
                    <p class="text-secondary-900 dark:text-darkmodetext"><b>{{__('Category Description')}}:</b> {{ $category->description }}</p>
                    <table class="min-w-full mt-4">
                        <thead class="bg-gray-50 dark:bg-secondary-200 text-left">
                            <tr>
                                <th class="px-1 pl-3 py-3">
                                    {{ __('ID') }}
                                </th>
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
                                    <tr id="{{ $product->id }}" data-id="{{ $product->id }}" data-order="{{ $product->order }}" data-category="{{ $category->id }}">
                                        <td class="py-2 px-4">
                                            {{ $product->id }}</td>
                                        <td class="py-2 px-4">
                                            {{ $product->name }}</td>
                                        <td class="py-2 px-4">
                                            {{ Str::limit($product->description, 50) }}</td>
                                        <td class="py-2">
                                            <a href="{{ route('admin.products.edit', $product->id) }}">
                                                <button class="button button-primary">
                                                    <i class="ri-pencil-line"></i> {{ __('Edit') }}
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
                                // Get all items by data-category
                                var products = document.querySelectorAll('[data-category="' + evt.item.parentNode.id + '"]');
                                // Loop through all items and update order
                                products.forEach(function(product) {
                                    product.setAttribute('data-order', product.rowIndex);
                                });
                                // Products should be array with {id, order}
                                products = Array.from(products).map(function(product) {
                                    return {
                                        id: product.id,
                                        order: product.getAttribute('data-order')
                                    }
                                });

                                // Send data to server
                                var data = {
                                    products: products,
                                    category: evt.item.parentNode.id,
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
    @endif

</x-admin-layout>
