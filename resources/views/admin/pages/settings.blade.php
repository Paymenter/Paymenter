<x-filament-panels::page>

    <x-filament-panels::form id="settings" wire:submit="save">

        {{ $this->form }}

        <x-filament-panels::form.actions :actions="$this->getFormActions()" />

    </x-filament-panels::form>
</x-filament-panels::page>
