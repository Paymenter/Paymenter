<x-app-layout clients title="{{ __('Product') }}">
    <x-slot name="title">
        {{ __('Product: ') }} {{ $product->name }}
    </x-slot>
    <!-- show next due date etc -->
    

</x-app-layout>