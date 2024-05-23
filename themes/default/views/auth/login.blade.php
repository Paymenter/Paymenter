<div class="flex min-h-[100vh]">
    <div class="justify-center flex flex-1 min-h-full flex-col mx-4">
        <div class="mx-auto container">
            <form
                class="mx-auto flex flex-col gap-2 mt-4 shadow-sm px-6 sm:px-14 pb-10 bg-primary-800 rounded-md xl:max-w-[40%] w-full"
                wire:submit="submit" id="login">
                <div class="flex flex-col items-center mt-4 mb-10">
                    <x-logo />
                    <h1 class="text-2xl text-center text-white mt-2">{{ __('Sign in to your account') }} </h1>
                </div>
                <x-form.input name="email" type="email" :label="__('Email')" :placeholder="__('Your email')" wire:model="email"
                    hideRequiredIndicator noDirty required />
                <x-form.input name="password" type="password" :label="__('Password')" :placeholder="__('Your password')" required
                    hideRequiredIndicator noDirty wire:model="password" />
                <div class="flex flex-row">
                    <x-form.checkbox name="remember" label="Remember me" wire:model="remember" />
                    <a class="text-sm text-secondary-500 text-secondary hover:underline ml-auto"
                        href="{{ route('password.reset') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>

                <x-captcha :form="'login'" />

                <x-button.primary class="w-full" type="submit">{{ __('Sign in') }}</x-button.primary>

                @if (config('settings.oauth_github') || config('settings.oauth_google') || config('settings.oauth_discord'))
                    <div class="flex flex-col items-center mt-4">
                        <div class="my-5 flex items-center w-full">
                            <span aria-hidden="true" class="h-0.5 grow rounded bg-primary-700"></span>
                            <span class="rounded-full px-3 py-1 text-xs font-medium bg-primary-700 text-gray-200">
                                {{ __('Or sign in with') }}
                            </span>
                            <span aria-hidden="true" class="h-0.5 grow rounded bg-primary-700"></span>
                        </div>
                        <div class="flex flex-row flex-wrap justify-center mt-2 gap-4">
                            @foreach (['github', 'google', 'discord'] as $provider)
                                @if (config('settings.oauth_' . $provider))
                                    <a href="{{ route('oauth.redirect', $provider) }}"
                                        class="flex items-center justify-center px-4 h-10 border border-primary-700 rounded-md text-primary-100">
                                        <img src="/assets/images/{{ $provider }}-dark.svg" alt="{{ $provider }}"
                                            class="w-5 h-5 mr-2 text-secondary">
                                        {{ __(ucfirst($provider)) }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="text-white text-center rounded-md py-2 mt-6 text-sm">
                    {{ __('Don\'t have an account yet?') }}
                    <a class="text-sm text-secondary-500 text-secondary hover:underline" href="{{ route('register') }}"
                        wire:navigate>
                        {{ __('Sign up') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
