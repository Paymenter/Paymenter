<x-app-layout>
    <x-slot name="title">
        {{ __('Profile') }}
    </x-slot>
    <!-- show form to edit user profile -->
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-darkmode2">
                <div class="p-6 bg-white border-b border-gray-200 sm:px-20 dark:bg-darkmode2 dark:border-black">
                    <h1 class="text-xl text-gray-500 dark:text-darkmodetext">Edit profile</h1>
                    <div class="grid grid-cols-1 gap-4">
                        <x-success class="mt-4" />
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
