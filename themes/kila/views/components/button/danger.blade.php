<x-button.primary {{ $attributes->merge(['class' => 'bg-red-700 bg-none text-white py-2 px-4 rounded hover:bg-red-600'])}}>
    {{ $slot }}
</x-button.primary>