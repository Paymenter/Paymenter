<x-app-layout>

    <div class="content min-h-[50vh] flex items-center justify-center flex-col">
        <div class="flex items-center text-secondary-900 font-semibold text-lg py-4 gap-x-2">
            <x-application-logo class="w-10" />
            {{ config('app.name', 'Paymenter') }}
        </div>
        <div class="content-box max-w-lg w-full">
            <form method="POST" action="{{ route('password.email') }}" id="forget-password">
                @csrf
                
                <h2 class="text-lg font-semibold">{{ __('Forgot Password') }}</h2>
                <p>
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </p>
                
                <x-input class="mt-3" label="{{ __('Email') }}" type="email" placeholder="{{ __('Email..') }}" required
                    name="email" id="email" icon="ri-at-line" />

                <x-recaptcha form="forget-password" />
                <div class="mt-3 flex justify-between items-center">
                    <a href="{{ route('login') }}" class="text-sm text-secondary-600 underline">
                        {{ __('Return to Login') }}
                    </a>
                    <button type="submit" class="button button-primary">
                        {{ __('Email Password Reset Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</x-app-layout>
