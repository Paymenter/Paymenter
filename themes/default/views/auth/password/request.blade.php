<form class="mx-auto flex flex-col gap-2 mt-4 px-6 sm:px-14 pb-10 bg-primary-800 rounded-md xl:max-w-[40%] w-full"
    wire:submit="submit" id="reset">
    <div class="flex flex-col items-center my-14">
        <x-logo class="h-10" />
        <h1 class="text-2xl text-center mt-6">{{ __('auth.reset_password') }} </h1>
    </div>
    <x-form.input name="email" type="text" :label="__('general.input.email')" :placeholder="__('general.input.email_placeholder')" wire:model="email" required />

    <x-captcha :form="'reset'" />

    <x-button.primary class="w-full" type="submit">{{ __('auth.reset_password') }}</x-button.primary>
</form>