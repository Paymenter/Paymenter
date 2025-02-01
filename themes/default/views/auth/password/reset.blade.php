<form class="flex flex-col gap-2 max-w-md mx-auto px-6 sm:px-14 pb-10 bg-primary-800 rounded-md" wire:submit="submit"
    id="reset">
    <div class="flex flex-col items-center mt-4 mb-10">
        <x-logo />
        <h1 class="text-2xl text-center mt-2">{{ __('auth.reset_password') }} </h1>
    </div>
    <x-form.input name="email" type="text" :label="__('auth.input.email_label')" :placeholder="__('auth.input.email')"
        wire:model="email" required disabled />
        
    <x-form.input name="password" type="password" :label="__('Password')" :placeholder="__('Your password')"
        wire:model="password" required />
    <x-form.input name="password_confirm" type="password" :label="__('Password')"
        :placeholder="__('Confirm your password')" wire:model="password_confirmation" required />

    <x-captcha :form="'reset'" />

    <x-button.primary class="w-full" type="submit">{{ __('auth.reset_password') }}</x-button.primary>
</form>