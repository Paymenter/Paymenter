<x-app-layout>
    <x-slot name="title">
        {{ __('Profile') }}
    </x-slot>

    <!-- show form to edit user profile -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg dark:bg-darkmode2">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200 dark:bg-darkmode2 dark:border-black">
                    <h1 class="dark:text-darkmodetext text-xl text-gray-500">Edit profile</h1>
                    <div class="grid grid-cols-1 gap-4">
                        <x-success class="mt-4" />
                        <div class="mt-6 text-gray-500 dark:text-darkmodetext dark:bg-darkmode2">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                <div class="mt-4">
                                    <label for="name">{{ __('normal.name') }}</label>
                                    <input id="name"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                        name="enabled" required type="text" value="{{ Auth::user()->name }}">
                                </div>
                                <div class="mt-4">
                                    <label for="address">{{ __('normal.address') }}</label>
                                    <input id="address"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                        name="address" required type="text" value="{{ Auth::user()->address }}">
                                </div>
                                <div class="mt-4">
                                    <label for="city">{{ __('normal.city') }}</label>
                                    <input id="city"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                        name="city" required type="text" value="{{ Auth::user()->city }}">
                                </div>
                                <div class="mt-4">
                                    <label for="country">{{ __('normal.country') }}</label>
                                    <input id="country"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                        name="country" required type="text" value="{{ Auth::user()->country }}">
                                </div>
                                <div class="mt-4">
                                    <label for="phone">{{ __('normal.phone') }}</label>
                                    <input id="phone"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md"
                                        name="phone" required type="text" value="{{ Auth::user()->phone }}">
                                </div>
                                <div class="flex items-center justify-end mt-4">
                                    <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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
