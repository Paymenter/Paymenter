<x-admin-layout>
    <x-slot name="title">
        {{ __('Products') }}
    </x-slot>
    <x-success class="mb-4" />

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2 dark:shadow-gray-700">
                <div class="p-6 bg-white sm:px-20 dark:bg-darkmode2">
                    <div class="mt-8 text-2xl dark:text-darkmodetext">
                        Update product {{ $product->name }}
                    </div>
                    <!-- extension a href -->
                    <div class="mt-6 text-gray-500 dark:text-darkmodetext ">
                        <a href="{{ route('admin.products.extension', $product->id) }}" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 dark:text-darkmodetext">Server settings</a>
                    </div>
                    <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                        <form method="POST" action="{{ route('admin.products.update', $product->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div>
                                <label for="name">{{ __('Name') }}</label>

                                <input id="name" class="block w-full mt-1 dark:bg-darkmode" type="text" name="name"
                                    value="{{ $product->name }} " required autofocus />
                            </div>
                            <div class="mt-4 ">
                                <label for="description">{{ __('Description') }}</label>

                                <textarea id="description" class="block w-full mt-1 dark:bg-darkmode" name="description" required>{{ $product->description }}</textarea>
                            </div>
                            <div class="mt-4">
                                <label for="price">{{ __('Price') }}</label>

                                {{ config('settings::currency_sign') }}<input id="price" class="block w-full mt-1 dark:bg-darkmode" type="number" name="price"
                                    min="0" step="0.01" value="{{ number_format($product->price, 2) }}" required />
                            </div>
                            <div class="mt-4">
                                <label for="image">{{ __('Image') }}</label>
                                <p>Only upload a new image if you want to replace the existing one</p>
                                <input id="image" class="block w-full mt-1 dark:bg-darkmode" type="file" name="image" @if($product->image == "null") disabled @endif/>
                                <div class="mt-2">
                                    <label for="no_image">No Image</label>
                                    <input type="checkbox" name="no_image" id="no_image" value="1" class="form-input w-4 h-4" @if($product->image == "null") checked @endif>
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-32 h-32 mt-4" id="prodctimg" onerror="removeElement(this)">
                                    <script>
                                        function removeElement(element) {
                                            element.classList.add('hidden');
                                        }
                                        document.getElementById('no_image').addEventListener('change', function() {
                                            document.getElementById('image').disabled = this.checked;
                                            document.getElementById('prodctimg').classList.toggle('hidden');
                                        });
                                        // Listen for file uploads then display the image
                                        document.getElementById('image').addEventListener('change', function() {
                                            if (this.files && this.files[0]) {
                                                var img = document.getElementById('prodctimg');
                                                img.classList.remove('hidden');
                                                img.src = URL.createObjectURL(this.files[0]);
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="category">{{ __('Category') }}</label>
                                <select id="category" class="block w-full mt-1 dark:bg-darkmode" name="category_id" required>
                                    @if ($categories->count())
                                        @foreach ($categories as $category)
                                            @if ($category->id == $product->category_id)
                                                <option value="{{ $category->id }}" selected>{{ $category->name }}
                                                </option>
                                            @else
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="">No categories found</option>
                                    @endif
                                </select>
                                <div class="flex items-center justify-end mt-4 text-blue-700">
                                    <a href="{{ route('admin.categories.create') }}">Create Category</a>
                                </div>

                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <button type="submit"
                                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 dark:text-darkmodetext">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
