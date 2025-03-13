<div class="flex flex-col gap-4">
    {{ __('services.cancel_are_you_sure') }}

    <x-form.select name="type" label="{{ __('services.cancel_type') }}" required wire:model="type">
        <option value="end_of_period">{{ __('services.cancel_end_of_period') }}</option>
        <option value="immediate">{{ __('services.cancel_immediate') }}</option>
    </x-form.select>

    <x-form.textarea name="reason" label="{{ __('services.cancel_reason') }}" required wire:model="reason" />

    <!-- Show you'll lose data warning if immediate cancellation is selected -->
    <template x-if="$wire.type === 'immediate'">
        <div class="bg-orange-700 text-white p-4 rounded">
            {{ __('services.cancel_immediate_warning') }}
        </div>
    </template>

    <x-button.danger wire:confirm="Are you sure?" wire:click="cancelService">
        {{ __('services.cancel') }}
    </x-button.danger>
</div>