<x-app-layout>
    <script>
        function removeElement(element) {
            element.remove();
            this.error = true;
        }
    </script>
    <x-slot name="title">
        {{ __('Checkout') }}
    </x-slot>
    <div class="content">
        <x-success />
        <livewire:checkout />
    </div>
</x-app-layout>
