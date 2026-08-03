<div class="container py-10">
    <div class="grid lg:grid-cols-2 gap-8 items-stretch">
        <x-cyber.auth-aside />

        <form class="cyber-card cyber-clip cyber-neon flex flex-col gap-2 px-6 sm:px-12 py-12 w-full justify-center"
            wire:submit="submit" id="login">
            <div class="flex flex-col items-center mb-10">
                <x-logo class="h-14 mb-2" />
                <h1 class="text-3xl font-black text-center mt-6 cyber-neon-text">
                    <span class="cyber-glitch" data-text="{{ __('auth.sign_in_title') }}">{{ __('auth.sign_in_title') }}</span>
                </h1>
                <p class="text-sm text-base/55 mt-2">Accede a tu panel de control</p>
            </div>

            <x-form.input name="email" type="email" :label="__('general.input.email')"
                :placeholder="__('general.input.email_placeholder')" wire:model="email" hideRequiredIndicator required autocomplete="email" />
            <x-form.input name="password" type="password" :label="__('general.input.password')"
                :placeholder="__('general.input.password_placeholder')" required hideRequiredIndicator wire:model="password" autocomplete="current-password" />
            <div class="flex flex-row">
                <x-form.checkbox name="remember" label="Remember me" wire:model="remember" />
                <a class="text-sm text-primary hover:underline ml-auto" href="{{ route('password.request') }}">
                    {{ __('auth.forgot_password') }}
                </a>
            </div>

            <x-captcha :form="'login'" />

            <x-button.primary class="w-full mt-4 cyber-sweep" type="submit">{{ __('auth.sign_in') }}</x-button.primary>

            {!! hook('auth.login') !!}

            @if (config('settings.oauth_github') || config('settings.oauth_google') || config('settings.oauth_discord'))
            <div class="flex flex-col items-center mt-4">
                <div class="my-5 flex items-center w-full">
                    <span aria-hidden="true" class="h-px grow rounded bg-neutral"></span>
                    <span class="rounded-full px-3 py-1 text-xs font-medium text-base/60">
                        {{ __('auth.or_sign_in_with') }}
                    </span>
                    <span aria-hidden="true" class="h-px grow rounded bg-neutral"></span>
                </div>
                <div class="flex flex-row flex-wrap justify-center mt-2 gap-4">
                    @foreach (['github', 'google', 'discord'] as $provider)
                    @if (config('settings.oauth_' . $provider))
                    <a href="{{ route('oauth.redirect', $provider) }}"
                        class="flex items-center justify-center px-4 h-10 border border-neutral rounded-md hover:border-primary/60 transition">
                        <img src="/assets/images/{{ $provider }}-dark.svg" alt="{{ $provider }}" class="size-5 mr-2">
                        {{ __(ucfirst($provider)) }}
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
            @if(!config('settings.registration_disabled', false))
            <div class="text-center rounded-md py-2 mt-6 text-sm text-base/70">
                {{ __('auth.dont_have_account') }}
                <a class="text-primary font-semibold hover:underline" href="{{ route('register') }}" wire:navigate>
                    {{ __('auth.sign_up') }}
                </a>
            </div>
            @endif
        </form>
    </div>
</div>
