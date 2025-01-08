<div class="grid grid-cols-4">
    @include('client.account.navigation')



    <div class="bg-primary-800 p-8 rounded-lg mt-2 col-span-3">
        <h4 class="text-2xl font-bold pb-3">{{ __('account.personal_details') }}</h4>
        <div class="grid grid-cols-2 gap-4">

            <x-form.input name="first_name" type="text" :label="__('general.input.first_name')"
                :placeholder="__('general.input.first_name_placeholder')" wire:model="first_name" required dirty />
            <x-form.input name="last_name" type="text" :label="__('general.input.last_name')"
                :placeholder="__('general.input.last_name_placeholder')" wire:model="last_name" required dirty />

            <x-form.input name="email" type="email" :label="__('general.input.email')"
                :placeholder="__('general.input.email_placeholder')" required wire:model="email" dirty />

            <x-form.properties :custom_properties="$custom_properties" :properties="$properties" dirty />
        </div>


        <x-button.primary wire:click="submit" class="w-full mt-4">
            {{ __('general.update') }}
        </x-button.primary>
    </div>
</div>