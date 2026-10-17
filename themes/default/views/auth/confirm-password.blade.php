<section class="mx-auto w-full max-w-lg py-14">
    <div class="bg-background-secondary rounded-lg p-6 sm:p-8">
        <h1 class="mb-2 text-2xl font-bold">{{ __('auth.confirm_password_title') }}</h1>
        <p class="mb-6 text-sm text-primary-300">{{ __('auth.confirm_password_description') }}</p>

        <form wire:submit="confirm" class="flex flex-col gap-4">
            <x-form.input name="password" type="password" :label="__('general.input.password')"
                :placeholder="__('general.input.password_placeholder')" wire:model="password"
                autocomplete="current-password" required autofocus />
            <x-button.primary type="submit" class="w-full">{{ __('auth.confirm_password') }}</x-button.primary>
        </form>
    </div>
</section>
