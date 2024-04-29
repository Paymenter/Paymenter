<x-filament-panels::page>

    <form id="settings" wire:submit="create">

        {{ $this->form }}

        <x-filament::button type="submit" class="mt-4">
            Save
        </x-filament::button>
    </form>

</x-filament-panels::page>
