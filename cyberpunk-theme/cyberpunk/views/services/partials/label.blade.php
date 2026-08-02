<div class="flex items-center text-base">
    <span class="mr-2">{{ __('services.name') }}:</span>
    <span class="text-base/50 flex gap-1" id="help-btn">
        @if($service->label !== $service->baseLabel)
            <x-tooltip :message="$service->baseLabel">
                {{ $service->label }}
            </x-tooltip>
        @else
            {{ $service->label }}
        @endif
        <button wire:click="$set('editLabel', true)" class="cursor-pointer">
            <x-loading target="editLabel" class="!size-4" />
            <span wire:loading.remove wire:target="editLabel">
                <x-ri-edit-line class="inline size-4 text-base/50" />
            </span>
        </button>
    </span>
</div>
@if($editLabel)
<x-modal :title="__('services.label_modal_title')" open="{{ $editLabel }}">
    <x-slot name="closeTrigger">
        <div class="flex gap-4">
            <button wire:click="$set('editLabel', false)" @click="open = false" class="text-primary-100">
                <x-ri-close-fill class="size-6" />
            </button>
        </div>
    </x-slot>
    <div class="space-y-4">
        <div>
            <x-form.input
                label="{{ __('services.label') }}"
                placeholder="{{ __('services.label_placeholder') }}"
                wire:model="label"
                name="label"
            />

            </div>
        <div class="flex justify-end">
            <x-button.primary wire:click="updateLabel" wire:loading.attr="disabled">
                {{ __('services.update_label') }}
            </x-button.primary>
        </div>
    </div>

</x-modal>
@endif
