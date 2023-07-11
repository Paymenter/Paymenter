<x-admin-layout>
    <x-slot name="title">
        {{ __('Categories') }}
    </x-slot>
    <div class="flex">
        <h1 class="text-2xl font-bold dark:text-darkmodetext flex-1">{{ __('Categories') }}</h1>
        <div class="flex justify-end pr-3 pt-3">
            <a href="{{ route('admin.categories.store') }}">
                <button class="button button-primary">
                    {{ __('Create') }}
                </button>
            </a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <div class="flex flex-wrap">
        <table class="min-w-full mt-4">
            <thead class="bg-gray-50 dark:bg-secondary-200 text-left">
                <tr>
                    <th class="px-1 pl-3 py-3">{{ __('Name') }}</th>
                    <th class="py-3">{{ __('Slug') }}</th>
                    <th class="py-3">{{ __('Actions') }}</th>
                    <th class="px-1 pr-2 py-3">{{ __('Order') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-secondary-100 divide-y divide-gray-200" id="categories">
                @foreach ($categories as $category)
                    <tr id="{{ $category->id }}">
                        <td class="py-2 px-4">
                            {{ $category->name }}</td>
                        <td class="py-2 px-4"><a href="{{ route('products', $category->slug) }}" target="_blank"
                                class="underline hover:text-blue-500 decoration-blue-500">{{ $category->slug }}</a>
                        </td>
                        <td class="py-2 px-4">
                            <div class="flex flew-wrap">
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                    class="mr-4 button button-primary">
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
                var data = {
                    id: evt.item.id,
                    newIndex: evt.newIndex,
                    oldIndex: evt.oldIndex,
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
