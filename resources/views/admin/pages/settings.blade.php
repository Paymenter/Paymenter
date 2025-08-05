<x-filament-panels::page>


    {{ $this->form }}

    <x-filament::actions
        :actions="[$this->saveAction]"
    />
</x-filament-panels::page>