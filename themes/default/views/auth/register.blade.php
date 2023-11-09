<x-app-layout>

    <div class="content min-h-[50vh] flex items-center justify-center flex-col">

    @if (config('settings::registrationAbillity_disable') == 1)
            <div class="max-w-lg w-full text-center  pb-7 pt-7 mb-5 bg-red-400 rounded-lg">
                <h1 class="font-bold text-red-800" >REGISTRATION IS CURRENTLY DISABLED</h1>
            </div>
        @else

        <div class="flex items-center text-secondary-900 font-semibold text-lg py-4 gap-x-2">
            <x-application-logo class="w-10" />
            {{ config('app.name', 'Paymenter') }}
        </div>

        <div class="content-box max-w-lg w-full">
            <form method="POST" action="{{ route('register') }}" id="register">
                @csrf

                <h2 class="text-lg font-semibold">{{ __('Make an Account') }}</h2>

                <x-input class="mt-3" label="{{ __('First name') }}" type="name" placeholder="{{ __('First name..') }}" required
                    name="first_name" id="first_name" icon="ri-user-3-line" />

                <x-input class="mt-3" label="{{ __('Last name') }}" type="name" placeholder="{{ __('Last name..') }}" required
                         name="last_name" id="last_name" icon="ri-user-3-line" />

                <x-input class="mt-3" label="{{ __('Email') }}" type="email" placeholder="{{ __('Email..') }}" required
                    name="email" id="email" icon="ri-at-line" />

                <x-input type="password" required class="mt-3" label="{{ __('Password') }}"
                    placeholder="{{ __('Password..') }}" name="password" id="password" icon="ri-lock-line"/>

                <x-input type="password" required class="mt-3" label="{{ __('Confirm Password') }}"
                    placeholder="{{ __('Password..') }}" name="password_confirmation" id="password-confirm" icon="ri-lock-password-line"/>

                <x-recaptcha form="register" />
                <div class="mt-3 flex justify-between items-center">
                    <a href="{{ route('login') }}" class="text-sm text-secondary-600 underline">
                        {{ __('Already registered?') }}
                    </a>
                    <button type="submit" class="button button-primary">
                        {{ __('Register') }}
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>

</x-app-layout>
