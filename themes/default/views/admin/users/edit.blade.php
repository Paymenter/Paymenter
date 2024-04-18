<div>
    @include('admin.users.nav')
    @if (!$user->email_verified_at)
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-2 flex flex-row justify-between"
            role="alert">
            <p class="font-semibold">User did not verify their email address yet.</p>
            <button wire:click="resendVerificationEmail" class="underline">
                <x-loading target="resendVerificationEmail" />
                <span wire:loading.remove>Resend Verification Email</span>
            </button>
        </div>
    @endif
    <form id="user" wire:submit.prevent="save">
        <div class="grid md:grid-cols-2 gap-x-4 gap-y-2 mt-2">

            <x-form.input :label="__('First Name')" :placeholder="__('First Name')" wire:model="first_name" name="first_name" />
            <x-form.input :label="__('Last Name')" :placeholder="__('Last Name')" wire:model="last_name" name="last_name" />

            <x-form.input :label="__('Email')" :placeholder="__('Email')" wire:model="email" name="email" required />
            <x-form.input :label="__('Phone')" :placeholder="__('Phone')" wire:model="phone" name="phone" />

            <x-form.input :label="__('Company name')" wire:model="company_name" name="company_name" />
            <x-form.input :label="__('Address')" wire:model="address_id" name="address" />

            <x-form.input :label="__('City')" :placeholder="__('City')" wire:model="city" name="city" />
            <x-form.input :label="__('State')" :placeholder="__('State')" wire:model="state" name="state" />

            <x-form.input :label="__('Zip')" :placeholder="__('Zip')" wire:model="zip" name="zip" />
            <x-form.select :label="__('Country')" wire:model="country" name="country" />

            <x-form.checkbox :label="__('Email Verified')" wire:model="email_verified" name="email_verified" />
        </div>

        <div class="w-min">
            <x-button.save>{{ __('Save') }}</x-button.save>
        </div>
    </form>
</div>
