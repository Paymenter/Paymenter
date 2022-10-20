<x-guest-layout>
    <div class="dark:bg-darkmode flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0">
        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 text-gray-500 fill-current" />
            </a>
        </div>

        <div class="dark:bg-darkmode2 w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md sm:max-w-md sm:rounded-lg">

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mt-4">
                    <label class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                        {{ __('Email') }}
                        @if (Route::has('register'))
                        <a style="float: right; margin-bottom: 1px;" class="dark:text-darkmodetext text-sm text-gray-600 underline hover:text-gray-900"
                            href="{{ route('register') }}">
                            {{ __('New here?') }} <b>{{ __('Create an account.') }}</b>
                        </a>
                    @endif
                    </label>

                    <input id="email" type="email"
                        class="dark:text-darkmodetext dark:bg-darkmode rounded-lg form-input w-full @error('email') border-red-500 @enderror" name="email"
                        value="{{ old('email') }}" required autocomplete="email">

                    @error('email')
                        <p class="dark:text-darkmodetext mt-1 text-xs italic text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                        {{ __('Password') }}
                    </label>

                    <input id="password" type="password"
                        class="dark:text-darkmodetext dark:bg-darkmode rounded-lg form-input w-full @error('password') border-red-500 @enderror" name="password" required
                        autocomplete="new-password">

                    @error('password')
                        <p class="dark:text-darkmodetext mt-1 text-xs italic text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="dark:text-darkmodetext inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="dark:bg-darkmode text-indigo-600 border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            name="remember">
                        <span class="dark:text-darkmodetext ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>
                @if(App\Models\Settings::first()->recaptcha == 1)
                <div class="g-recaptcha" data-sitekey="{{App\Models\Settings::first()->recaptcha_site_key }}"></div>
                @endif
                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="dark:text-darkmodetext text-sm text-gray-600 underline hover:text-gray-900"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                    <button type="submit"
                        class="dark:text-darkmodetext inline-flex items-center px-4 py-2 ml-4 text-xs font-semibold tracking-widest uppercase transition duration-150 ease-in-out bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
