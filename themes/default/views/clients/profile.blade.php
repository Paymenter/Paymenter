<x-app-layout>
    <x-slot name="title">
        {{ __('Profile') }}
    </x-slot>
    <!-- show form to edit user profile -->
    <x-success class="mt-4" />
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2">
                <div class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2 dark:border-black">
                    <h1 class="text-xl text-gray-500 dark:text-darkmodetext">{{ __('Edit profile') }}</h1>
                    @isset($secret)
                        <button data-modal-target="tfa" data-modal-toggle="tfa"
                            class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 mt-2"
                            type="button">
                            {{ __('Setup Two Factor Authentication') }}
                        </button>
                        <div id="tfa" tabindex="-1" aria-hidden="true"
                            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] md:h-full">
                            <div class="relative w-full h-full max-w-2xl md:h-auto">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <div
                                        class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            {{ __('Two Factor Authentication') }}
                                        </h3>
                                        <button type="button"
                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                            data-modal-hide="defaultModal">
                                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <div class="p-6 space-y-6">
                                        <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                            {{ __('Scan the QR code below with your authenticator app. If you do not have an authenticator app, you can use the code below to manually enter it.') }}
                                        </p>
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <img src="{{ $qr }}" alt="QR Code" />
                                            </div>
                                            <div class="flex flex-col items-center justify-center">
                                                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                                    {{ __('Or enter this code manually:') }}
                                                </p>
                                                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                                    {{ $secret }}
                                                </p>
                                            </div>
                                        </div>
                                        <form method="POST" action="{{ route('clients.profile.tfa') }}">
                                            @csrf
                                            <input type="hidden" name="secret" value="{{ $secret }}">
                                            <div class="mt-4">
                                                <label for="code">{{ __('Code') }}</label>
                                                <input id="code"
                                                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                                    name="code" required type="text">
                                            </div>
                                            <div class="mt-4">
                                                <label for="code">{{ __('Password') }}</label>
                                                <input id="password"
                                                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                                    name="password" required type="password">
                                            </div>
                                            <div class="flex items-center justify-end mt-4">
                                                <button
                                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                                    type="submit">
                                                    {{ __('Submit') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <button data-modal-target="tfa" data-modal-toggle="tfa"
                            class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 mt-2"
                            type="button">
                            {{ __('Disable Two Factor Authentication') }}
                        </button>
                        <div id="tfa" tabindex="-1" aria-hidden="true"
                            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] md:h-full">
                            <div class="relative w-full h-full max-w-2xl md:h-auto">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <div
                                        class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            {{ __('Two Factor Authentication') }}
                                        </h3>
                                        <button type="button"
                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                            data-modal-hide="defaultModal">
                                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <div class="p-6 space-y-6">
                                        <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                            {{ __('Please enter your password to disable two factor authentication.') }}
                                            {{ __('This will remove the two factor authentication from your account. Making your account more vulnerable.') }}
                                        </p>
                                        <form method="POST" action="{{ route('clients.profile.tfa') }}">
                                            @csrf
                                            <input type="hidden" name="disable" value="true">
                                            <div class="mt-4">
                                                <label for="password">{{ __('Password') }}</label>
                                                <input id="password"
                                                    class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                                    name="password" required type="password">
                                            </div>
                                            <div class="flex items-center justify-end mt-4">
                                                <button
                                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                                    type="submit">
                                                    {{ __('Submit') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endisset
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                            <form method="POST" action="{{ route('clients.profile.update') }}">
                                @csrf
                                <div class="mt-4">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input id="name"
                                        class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                        name="name" required type="text" value="{{ Auth::user()->name }}">
                                </div>
                                <div class="mt-4">
                                    <label for="address">{{ __('Address') }}</label>
                                    <input id="address"
                                        class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                        name="address" required type="text" value="{{ Auth::user()->address }}">
                                </div>
                                <div class="mt-4">
                                    <label for="city">{{ __('City') }}</label>
                                    <input id="city"
                                        class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                        name="city" required type="text" value="{{ Auth::user()->city }}">
                                </div>
                                <div class="mt-4">
                                    <label for="country">{{ __('Country') }}</label>
                                    <input id="country"
                                        class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                        name="country" required type="text" value="{{ Auth::user()->country }}">
                                </div>
                                <div class="mt-4">
                                    <label for="phone">{{ __('Phone') }}</label>
                                    <input id="phone"
                                        class="block w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-darkmode"
                                        name="phone" required type="text" value="{{ Auth::user()->phone }}">
                                </div>
                                <div class="flex items-center justify-end mt-4">
                                    <button type="submit"
                                        class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
