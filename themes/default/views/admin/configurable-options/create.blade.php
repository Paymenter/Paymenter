<x-admin-layout title="Create new Configurable Option Group">
    <h1 class="text-2xl font-bold dark:text-darkmodetext">{{ __('Create new Configurable Option Group') }}</h1>
    <div class="p-6">
        <!-- create a new configurable option group -->
        <form method="POST" action="{{ route('admin.configurable-options.store') }}">
            @csrf
            <x-input id="name" class="block mt-1 w-full" type="text" name="name" label="Name" placeholder="Name"
                required autofocus />
            <x-input id="description" class="block mt-1 w-full" type="text" name="description" label="Description"
                placeholder="Description" required autofocus />
            <!-- MultiSelect -->
            <x-input id="products" class="block mt-1 w-full" type="select" name="products[]"
                label="Products" placeholder="Products" required multiple>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </x-input>
            <button type="submit" class="button button-success mt-4 w-full">
                {{ __('Create') }}
            </button>
        </form>
    </div>
</x-admin-layout>
