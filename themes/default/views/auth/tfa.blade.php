<x-guest-layout>
    <div class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 dark:bg-darkmode sm:justify-center sm:pt-0">
        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 text-gray-500 fill-current" />
            </a>
        </div>

        <div
            class="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md dark:bg-darkmode2 sm:max-w-md sm:rounded-lg">

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('tfa') }}" id="tfa">
                @csrf
                <h4 class="text-base text-gray-600 dark:text-darkmodetext">
                    {{ __('Please enter the code from your authenticator app.') }}
                </h4>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                        {{ __('Code') }}
                    </label>

                    <input id="code" type="text"
                        class="dark:text-darkmodetext dark:bg-darkmode rounded-lg form-input w-full @error('code') border-red-500 @enderror"
                        name="code" required>

                    @error('code')
                        <p class="mt-1 text-xs italic text-red-500 dark:text-darkmodetext">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <br>
                <div class="flex items-center justify-center">
                    <x-recaptcha form="tfa" />
                </div>
                <div class="flex items-center justify-end mt-4">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 ml-4 text-xs font-semibold tracking-widest uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md dark:text-darkmodetext hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25">
                        {{ __('Submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
