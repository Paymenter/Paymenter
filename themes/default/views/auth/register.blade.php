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

        <div class="content-box max-w-2xl w-full">
            <form method="POST" action="{{ route('register') }}" id="register">
                @csrf

                <h2 class="text-lg font-semibold">{{ __('Make an Account') }}</h2>
                <div class="flex flex-row gap-4">
                    <x-input class="mt-3 w-full" label="{{ __('First name') }}" type="name" placeholder="{{ __('First name..') }}" required
                        name="first_name" id="first_name" icon="ri-user-3-line" />

                    <x-input class="mt-3 w-full" label="{{ __('Last name') }}" type="name" placeholder="{{ __('Last name..') }}" required
                             name="last_name" id="last_name" icon="ri-user-3-line" />
                </div>

                <x-input class="mt-3" label="{{ __('Email') }}" type="email" placeholder="{{ __('Email..') }}" required
                    name="email" id="email" icon="ri-at-line" />
                <div class="flex flex-row gap-4">
                    @if(config('settings::requiredClientDetails_address') == 1)
                        <x-input class="mt-3 w-full" label="{{ __('Address') }}" type="text" placeholder="{{ __('Address..') }}"
                            name="address" id="address" icon="ri-home-4-line" />
                    @endif
                    @if(config('settings::requiredClientDetails_city') == 1)
                        <x-input class="mt-3 w-full" label="{{ __('City') }}" type="text" placeholder="{{ __('City..') }}" required
                            name="city" id="city" icon="ri-building-2-line" />
                    @endif
                </div>
                <div class="flex flex-row gap-4">
                    @if(config('settings::requiredClientDetails_phone') == 1)
                        <x-input class="mt-3 w-full" label="{{ __('Phone') }}" type="text" placeholder="{{ __('Phone..') }}" required
                            name="phone" id="phone" icon="ri-phone-line" />
                    @endif
                    @if(config('settings::requiredClientDetails_zip') == 1)
                        <x-input class="mt-3 w-full" label="{{ __('Zip') }}" type="text" placeholder="{{ __('Zip..') }}" required
                            name="zip" id="zip" icon="ri-building-2-line" />
                    @endif
                </div>
                @if(config('settings::requiredClientDetails_country') == 1)
                    <x-input type="select" class="mt-3 w-full" placeholder="{{ __('Country') }}" name="country"
                        id="country" label="{{ __('Country') }}" required="required">
                        @foreach (App\Classes\Constants::countries() as $key => $country)
                            <option value="{{ $key }}">
                                {{ $country }}
                            </option>
                        @endforeach
                    </x-input>
                @endif
                <div class="flex flex-row gap-4">
                    <x-input type="password" required class="mt-3 w-full" label="{{ __('Password') }}"
                        placeholder="{{ __('Password..') }}" name="password" id="password" icon="ri-lock-line"/>

                    <x-input type="password" required class="mt-3 w-full" label="{{ __('Confirm Password') }}"
                        placeholder="{{ __('Password..') }}" name="password_confirmation" id="password-confirm" icon="ri-lock-password-line"/>
                </div>
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
