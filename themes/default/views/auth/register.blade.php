<form
    class="mx-auto flex flex-col gap-2 mt-4 px-6 sm:px-14 pb-10 bg-primary-800 rounded-md xl:max-w-[60%] w-full"
    wire:submit.prevent="submit" id="register">
    <div class="flex flex-col items-center my-14">
        <x-logo class="h-10" />
        <h1 class="text-2xl text-center mt-6">{{ __('auth.sign_up_title') }} </h1>
    </div>
    <div class="flex flex-col md:grid md:grid-cols-2 gap-4">
        <x-form.input name="first_name" type="text" :label="__('general.input.first_name')"
            :placeholder="__('general.input.first_name_placeholder')" wire:model="first_name" required />
        <x-form.input name="last_name" type="text" :label="__('general.input.last_name')"
            :placeholder="__('general.input.last_name_placeholder')" wire:model="last_name" required />

        <x-form.input name="email" type="email" :label="__('general.input.email')"
            :placeholder="__('general.input.email_placeholder')" required wire:model="email" divClass="col-span-2" />

        <x-form.input name="password" type="password" :label="__('general.input.password')" :placeholder="__('general.input.password_placeholder')"
            wire:model="password" required />
        <x-form.input name="password_confirm" type="password" :label="__('general.input.password_confirmation')"
            :placeholder="__('general.input.password_confirmation_placeholder')" wire:model="password_confirmation" required />

        <x-form.properties :custom_properties="$custom_properties" :properties="$properties" />
    
        @if(config('settings.tos'))
            <x-form.checkbox wire:model="tos" name="tos" required>
                {{ __('product.tos') }}
                <a href="{{ config('settings.tos') }}" target="_blank" class="text-primary hover:text-primary/80">
                    {{ __('product.tos_link') }}
                </a>
            </x-form.checkbox>
        @endif    
    </div>

    <x-captcha :form="'register'" />

    <x-button.primary class="w-full mt-2">{{ __('auth.sign_up') }}</x-button.primary>

    <div class="text-base text-center rounded-md py-2 mt-6 text-sm">
        {{ __('auth.already_have_account') }}
        <a class="text-sm text-secondary-500 text-secondary hover:underline" href="{{ route('login') }}" wire:navigate>
            {{ __('auth.sign_in') }}
        </a>
    </div>
</form>
