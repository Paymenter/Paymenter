<x-admin-layout>
    <x-slot name="title">
        {{ __('Categories') }}
    </x-slot>
    <div class="p-3 bg-white dark:bg-secondary-100 flex flex-row justify-between">
        <div>
            <div class="mt-3 text-2xl font-bold dark:text-darkmodetext">
                {{ __('Categories') }}
            </div>
            <div class="mt-3 text-gray-500 dark:text-darkmodetext">
                {{ __('Here you can see all categories.') }}
            </div>
        </div>
        <div class="flex my-auto float-end justify-end mr-4">
            <a href="{{ route('admin.categories.store') }}"
               class="px-4 py-2 font-bold text-white transition rounded delay-400 bg-blue-500 button button-primary">
                <i class="ri-user-add-line"></i> {{ __('Create') }}
            </a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <div class="flex flex-wrap">
        <table class="min-w-full mt-4">
            <thead class="bg-gray-50 dark:bg-secondary-200 text-left">
                <tr>
                    <th class="px-1 pl-3 py-3">{{ __('ID') }}</th>
                    <th class="px-1 pl-3 py-3">{{ __('Name') }}</th>
                    <th class="py-3">{{ __('Slug') }}</th>
                    <th class="py-3">{{ __('Actions') }}</th>
                    <th class="px-1 pr-2 py-3">{{ __('Order') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-secondary-100 divide-y divide-gray-200" id="categories">
                @foreach ($categories as $category)
                    <tr id="{{ $category->id }}" data-id="{{ $category->id }}" data-order="{{ $category->order }}">
                        <td class="py-2 px-4">{{ $category->id }}</td>
                        <td class="py-2 px-4">{{ $category->name }}</td>
                        <td class="py-2 px-4">
                            <a href="{{ route('products', $category->slug) }}" target="_blank" class="hover:underline hover:text-blue-500 decoration-blue-500">
                                {{ $category->slug }}
                            </a>
                        </td>
                        <td class="py-2 px-4">
                            <div class="flex flew-wrap">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="mr-4 button button-primary">
                                    {{ __('Edit') }}
                                </a>
                                <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button-danger">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="w-10 text-2xl ml-4 font-thin text-center draggable">
                            <i class="ri-drag-move-2-line draggable"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        var el = document.getElementById('categories');
        var sortable = Sortable.create(el, {
            animation: 150,
            ghostClass: 'bg-gray-100',
            chosenClass: 'bg-secondary-200',
            handle: '.draggable',
            onEnd: function(evt) {
                var url = "{{ route('admin.categories.reorder') }}";
                var categories = document.querySelectorAll('#categories tr');
                categories.forEach(function(category) {
                    category.setAttribute('data-order', category.rowIndex);
                });
                categories = Array.from(categories).map(function(category) {
                    return {
                        id: category.getAttribute('data-id'),
                        order: category.getAttribute('data-order'),
                    };
                });

                var data = {
                    categories: categories,
                    _token: '{{ csrf_token() }}'
                };
                // Plain JavaScript
                var request = new XMLHttpRequest();
                request.open('POST', url, true);
                request.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
                request.send(JSON.stringify(data));

                request.onload = function() {
                    if (request.status >= 200 && request.status < 400) {
                        var resp = request.responseText;

                    } else {
                        console.log('error');
                    }
                };

            },
        });
    </script>

</x-admin-layout>
