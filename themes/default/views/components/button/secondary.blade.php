<x-button.primary {{ $attributes->merge(['class' => '!bg-background-secondary text-white py-2 px-4 rounded hover:bg-primary-600'])}}>
    {{ $slot }}
</x-button.primary>