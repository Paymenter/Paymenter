<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>
    <a href="/stripe" class="text-xl">CHECKOUT</a>
    @dump($products)
</x-app-layout>
