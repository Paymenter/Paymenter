<form
    class="mx-auto flex flex-col gap-2 mt-4 px-6 sm:px-14 pb-10 bg-primary-800 rounded-md xl:max-w-[40%] w-full"
    wire:submit="submit" id="login">
    <div class="flex flex-col items-center my-14">
        <x-logo class="h-10" />
        <h1 class="text-2xl text-center mt-6">{{ __('auth.sign_in_title') }} </h1>
    </div>
    <x-form.input name="email" type="email" :label="__('general.input.email')"
        :placeholder="__('general.input.email_placeholder')" wire:model="email" hideRequiredIndicator required />
    <x-form.input name="password" type="password" :label="__('general.input.password')"
        :placeholder="__('general.input.password_placeholder')" required hideRequiredIndicator wire:model="password" />
    <div class="flex flex-row">
        <x-form.checkbox name="remember" label="Remember me" wire:model="remember" />
        <a class="text-sm text-secondary-500 text-secondary hover:underline ml-auto"
            href="{{ route('password.request') }}">
            {{ __('auth.forgot_password') }}
        </a>
    </div>

    <x-captcha :form="'login'" />

    <x-button.primary class="w-full" type="submit">{{ __('auth.sign_in') }}</x-button.primary>

    @if (config('settings.oauth_github') || config('settings.oauth_google') || config('settings.oauth_discord'))
    <div class="flex flex-col items-center mt-4">
        <div class="my-5 flex items-center w-full">
            <span aria-hidden="true" class="h-0.5 grow rounded bg-primary-700"></span>
            <span class="rounded-full px-3 py-1 text-xs font-medium bg-primary-700 text-gray-200">
                {{ __('auth.or_sign_in_with') }}
            </span>
            <span aria-hidden="true" class="h-0.5 grow rounded bg-primary-700"></span>
        </div>
        <div class="flex flex-row flex-wrap justify-center mt-2 gap-4">
            @foreach (['github', 'google', 'discord'] as $provider)
            @if (config('settings.oauth_' . $provider))
            <a href="{{ route('oauth.redirect', $provider) }}"
                class="flex items-center justify-center px-4 h-10 border border-neutral rounded-md text-primary-100">
                <img src="/assets/images/{{ $provider }}-dark.svg" alt="{{ $provider }}"
                    class="size-5 mr-2 text-secondary">
                {{ __(ucfirst($provider)) }}
            </a>
            @endif
            @endforeach
        </div>
    </div>
    @endif
    @if(!config('settings.registration_disabled', false))
    <div class="text-base text-center rounded-md py-2 mt-6 text-sm">
        {{ __('auth.dont_have_account') }}
        <a class="text-sm text-secondary-500 text-secondary hover:underline" href="{{ route('register') }}"
            wire:navigate>
            {{ __('auth.sign_up') }}
        </a>
    </div>
    @endif
</form>