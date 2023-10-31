<x-app-layout>
    <x-slot name="title">
        {{ __('Checkout') }}
    </x-slot>
    <!-- form to configure the product -->
    <x-success class="m-2 mb-4" />
    <livewire:checkout.config :product="$product" />
</x-app-layout>
