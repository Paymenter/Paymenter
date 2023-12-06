<x-admin-layout title="Create Invoice">
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Create Invoice') }}</h1>
    <form action="{{ route('admin.invoices.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-2 gap-4 mt-4">
                <x-input type="searchselect" name="user_id" required placeholder="Select User" label="User">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </x-input>
            <div class="w-full col-span-2">
                <label class="block dark:text-darkmodetext" for="code">
                    {{ __('Items') }}
                </label>
                <div class="grid grid-cols-2 gap-4 mt-4" id="items">
                    <div class="w-full">
                        <label class="block dark:text-darkmodetext" for="code">
                            {{ __('Item Name') }}
                        </label>
                        <input
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                            type="text" name="item_name[]" id="item_name" required placeholder="Item Name">
                    </div>
                    <div class="w-full">
                        <label class="block dark:text-darkmodetext" for="code">
                            {{ __('Item Price') }}
                        </label>
                        <input
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                            type="number" name="item_price[]" id="item_price" required placeholder="Item Price" step="0.01" min="0">
                    </div>
                </div>
                <div class="grid-cols-2">
                    <button type="button" id="add-item" class="button button-secondary m-2 float-right">
                        {{ __('Add Item') }}
                    </button>
                </div>
                <script>
                    document.getElementById('add-item').addEventListener('click', function() {
                        var item = document.createElement('div');
                        item.classList.add('w-full');
                        item.innerHTML = `
                            <label class="block dark:text-darkmodetext" for="code">
                                {{ __('Item Name') }}
                            </label>
                            <input
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                type="text" name="item_name[]" id="item_name" required placeholder="Item Name">
                        `
                        document.getElementById('items').appendChild(item);
                        item = document.createElement('div');
                        item.classList.add('w-full');
                        item.innerHTML = `
                            <label class="block dark:text-darkmodetext" for="code">
                                {{ __('Item Price') }}
                            </label>
                            <input
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                type="number" name="item_price[]" id="item_price" required placeholder="Item Price" step="0.01" min="0">
                        `;
                        document.getElementById('items').appendChild(item);
                    });
                </script>
            </div>
            <!-- End Items array -->
        </div>
        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="button button-primary">
                {{ __('Create Invoice') }}
            </button>
        </div>
    </form>
</x-admin-layout>
