<x-center>
    <form class="flex flex-col gap-2" wire:submit.prevent="submit" id="register">
        <div class="flex flex-col items-center mt-4 mb-10">
            <x-logo />
            <h1 class="text-2xl text-center text-white mt-2">{{ __('auth.sign_up_title') }} </h1>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <x-form.input name="first_name" type="text" :label="__('general.input.first_name')" :placeholder="__('general.input.first_name_placeholder')" wire:model="first_name"
                :required="!in_array('first_name', config('settings.optional_fields'))" noDirty />
            <x-form.input name="last_name" type="text" :label="__('general.input.last_name')" :placeholder="__('general.input.last_name_placeholder')" wire:model="last_name"
                :required="!in_array('last_name', config('settings.optional_fields'))" noDirty />

            <x-form.input name="email" type="email" :label="__('general.input.email')" :placeholder="__('general.input.email_placeholder')" required wire:model="email"
                noDirty />
            <x-form.input name="phone" type="phone" :label="__('general.input.phone')" :placeholder="__('general.input.phone_placeholder')" wire:model="phone"
                :required="!in_array('phone', config('settings.optional_fields'))" noDirty />

            <x-form.input name="company_name" type="text" :label="__('general.input.company_name')" :placeholder="__('general.input.company_name_placeholder')"
                wire:model="company_name" :required="!in_array('company_name', config('settings.optional_fields'))" noDirty />
            <x-form.select name="country" type="text" :label="__('general.input.country')" :placeholder="__('general.input.country_placeholder')" wire:model="country"
                :required="!in_array('country', config('settings.optional_fields'))" :options="config('app.countries')" noDirty />
            <x-form.input name="address" type="text" :label="__('general.input.address')" :placeholder="__('general.input.address_placeholder')" wire:model="address"
                :required="!in_array('address', config('settings.optional_fields'))" divClass="col-span-2" noDirty />
            <x-form.input name="address2" type="text" :label="__('general.input.address2')" :placeholder="__('general.input.address2_placeholder')" wire:model="address2"
                :required="!in_array('address2', config('settings.optional_fields'))" divClass="col-span-2" noDirty />

            <div class="grid grid-cols-3 col-span-2 gap-4">
                <x-form.input name="city" type="text" :label="__('general.input.city')" :placeholder="__('general.input.city_placeholder')" wire:model="city"
                    :required="!in_array('city', config('settings.optional_fields'))" noDirty />
                <x-form.input name="state" type="text" :label="__('general.input.state')" :placeholder="__('general.input.state_placeholder')" wire:model="state"
                    :required="!in_array('state', config('settings.optional_fields'))" noDirty />
                <x-form.input name="zip" type="text" :label="__('general.input.zip')" :placeholder="__('general.input.zip_placeholder')" wire:model="zip"
                    :required="!in_array('zip', config('settings.optional_fields'))" noDirty />
            </div>

            <x-form.input name="password" type="password" :label="__('Password')" :placeholder="__('Your password')" wire:model="password"
                required noDirty />
            <x-form.input name="password_confirm" type="password" :label="__('Password')" :placeholder="__('Confirm your password')"
                wire:model="password_confirmation" required noDirty />
        </div>

        <x-captcha :form="'register'" />

        <x-button.primary class="w-full">{{ __('Sign up') }}</x-button.primary>

        <div class="text-white text-center rounded-md py-2 mt-6 text-sm">
            {{ __('Already have an account?') }}
            <a class="text-sm text-secondary-500 text-secondary hover:underline" href="{{ route('login') }}"
                wire:navigate>
                {{ __('Sign in') }}
            </a>
        </div>
    </form>
</x-center>
