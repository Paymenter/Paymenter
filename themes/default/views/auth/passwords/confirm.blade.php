<x-app-layout>
    <div class="min-h-[50vh] flex items-center justify-center flex-col">
        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 text-gray-500 fill-current" />
            </a>
        </div>
        
        <div class="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md sm:max-w-md sm:rounded-lg content-box">
            <div class="mb-4 text-sm">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" id="pw-confirm">
                @csrf

                <!-- Password -->
                <div class="mt-4">

                    <x-input id="password" type="password" label="{{ __('Password') }}"
                        name="password" required autocomplete="new-password" >
                    </x-input>
                </div>
                <x-recaptcha form="pw-confirm" />
                <div class="flex justify-end mt-4">
                    <button type="submit"
                        class="button button-primary">
                        {{ __('Confirm') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
