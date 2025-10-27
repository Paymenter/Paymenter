<form
    class="flex flex-col gap-2 max-w-md mx-auto"
    wire:submit="verify">
    <div class="flex flex-col items-center mt-4 mb-10">
        <x-logo class="h-10" />
        <h1 class="text-2xl text-center mt-2">{{ __('auth.verify_2fa') }} </h1>
    </div>
    <x-form.input name="code" type="text" :label="__('account.input.two_factor_code')" :placeholder="__('account.input.two_factor_code_placeholder')" wire:model="code" required />

    <x-button.primary class="w-full" type="submit">{{ __('auth.verify') }}</x-button.primary>
</form>