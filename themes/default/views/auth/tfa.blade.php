<x-app-layout>
    <div class="content min-h-[50vh] flex items-center justify-center flex-col">
        <div class="flex items-center text-secondary-900 font-semibold text-lg py-4 gap-x-2">
            <x-application-logo class="w-10" />
            {{ config('app.name', 'Paymenter') }}
        </div>

        <div class="content-box max-w-lg w-full">
            <form method="POST" action="{{ route('tfa') }}" id="tfa">
                @csrf
                <h2 class="text-lg font-semibold">{{ __('Two Factor Authentication') }}</h2>
                <h4 class="text-base text-gray-600 dark:text-darkmodetext">
                    {{ __('Please enter the code from your authenticator app.') }}
                </h4>

                <x-input id="code" type="text" label="{{ __('Code') }}" name="code" required class="mt-3" />
                <div class="flex items-center justify-center">
                    <x-recaptcha form="tfa" />
                </div>
                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="button button-primary">
                        {{ __('Submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
