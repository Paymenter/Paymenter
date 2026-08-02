<div class="container mt-10 pb-10">
    <x-navigation.breadcrumb />

    @if(cyber_ext() && cyber_bool('avatar_uploads_enabled', true))
    <div class="mt-4">
        <livewire:cyberpunk.avatar />
    </div>
    @endif

    <div class="cyber-card cyber-clip p-6 mt-4">
        <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
            <x-ri-user-3-fill class="size-5 text-primary" />
            {{ __('navigation.personal_details') }}
        </h2>

        <div class="grid md:grid-cols-2 gap-4">
            <x-form.input name="first_name" type="text" :label="__('general.input.first_name')"
                :placeholder="__('general.input.first_name_placeholder')" wire:model="first_name" required dirty />
            <x-form.input name="last_name" type="text" :label="__('general.input.last_name')"
                :placeholder="__('general.input.last_name_placeholder')" wire:model="last_name" required dirty />

            <x-form.input name="email" type="email" :label="__('general.input.email')"
                :placeholder="__('general.input.email_placeholder')" required wire:model="email" dirty />

            <x-form.properties :custom_properties="$custom_properties" :properties="$properties" dirty />
        </div>

        <x-button.primary wire:click="submit" class="w-full mt-6">
            {{ __('general.update') }}
        </x-button.primary>
    </div>
</div>
