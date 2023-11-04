<x-app-layout>
    <div id="app"
        class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 dark:text-darkmodetext dark:bg-darkmode sm:justify-center sm:pt-0">
        <div
            class="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md dark:bg-secondary-100 sm:max-w-md sm:rounded-lg">
            <div class="text-center">
                <h2 class="text-2xl font-bold">{{ __('Reset Password') }}</h2>
            </div>
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('change-password.update') }}">
                @csrf
                <!-- Current Password -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 bg-white dark:text-white dark:bg-secondary-100">
                        {{ __('Current Password') }}
                    </label>

                    <input id="current_password" type="password"
                        class="mt-1.5 dark:bg-darkmode rounded-lg form-input w-full  @error('current_password') border-red-500 @enderror"
                        name="current_password" autocomplete="current_password">

                </div>

                <!-- New Password -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 bg-white dark:text-white dark:bg-secondary-100">
                        {{ __('New Password') }}
                    </label>

                    <input id="new_password" type="password"
                        class="mt-1.5 dark:bg-darkmode rounded-lg form-input w-full " name="new_password"
                        autocomplete="new_password">
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 bg-white dark:text-white dark:bg-secondary-100">
                        {{ __('Confirm New Password') }}
                    </label>

                    <input id="new_password_confirmation" type="password"
                        class="mt-1.5 dark:bg-darkmode rounded-lg form-input w-full " name="new_password_confirmation"
                        autocomplete="new_password_confirmation">
                </div>
                <x-recaptcha />
                <!-- Save button and Cancel button -->
                <div class="flex items-center justify-end mt-4">
                    <button class="form-submit">
                        {{ __('Save Changes') }}
                    </button>
                    <button class="form-submit bg-gray-500 hover:bg-gray-600 ml-4">
                        @if (url()->previous() == url()->current())
                            <a href="{{ url('/') }}">{{ __('Cancel') }}</a>
                        @else
                            <a href="{{ url()->previous() }}">{{ __('Cancel') }}</a>
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
