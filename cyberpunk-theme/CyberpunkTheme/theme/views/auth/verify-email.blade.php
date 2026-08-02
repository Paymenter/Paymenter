<div
    class="mx-auto flex flex-col gap-2 mt-4 px-6 sm:px-14 py-10 bg-background-secondary rounded-md xl:max-w-[60%] w-full">
    <h1 class="text-2xl">{{ __('auth.verification.notice') }}</h1>
    <p class="mt-2">{{ __('auth.verification.check_your_email') }}</p>

    <form class="flex flex-col gap-2 mt-4" wire:submit.prevent="submit" id="verify-email">
        <x-captcha :form="'verify-email'" />

        <p class="text-base">{{ __('auth.verification.not_received') }}</p>
        <x-button.primary class="w-full" type="submit">{{ __('auth.verification.request_another') }}</x-button.primary>
    </form>
</div>