<x-admin-layout>
    <x-slot name="title">
        {{ __('Clients') }}
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clients') }}
        </h2>

    </x-slot>
    <!-- create client -->
    <div class="dark:bg-darkmode2 p-6 sm:px-20 bg-white">
        <div class="dark:text-darkmodetext mt-8 text-2xl">
            {{ __('Create client') }}
        </div>
        <div class="dark:text-darkmodetext mt-6 text-gray-500">
            {{ __('Here you can create a new client.') }}
        </div>
    </div>

    <div class="dark:bg-darkmode2 bg-gray-200 bg-opacity-25 grid grid-cols-1">
        <div class="p-6">
            <div class="flex items-center">
                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">
                    <form method="POST" action="{{ route('admin.clients.store') }}">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="name"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Name') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="name" id="name" placeholder="John Doe"
                                        autocomplete="name" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="email"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Email') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="email" id="email" placeholder="jdoe@example.com"
                                        autocomplete="email" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="phone"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Phone') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="phone" placeholder="+1-234-567-89" id="phone"
                                        autocomplete="phone"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="companynames"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Company Names') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" placeholder="Optional" name="companynames" id="companynames"
                                        autocomplete="companynames"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="password"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Password') }}
                                </label>
                                <div class="mt-1">
                                    <input type="password" name="password" id="password" placeholder="*******"
                                        autocomplete="new-password" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                        </div>

                        <hr class="my-6 border-b-1 border-gray-300 dark:border-gray-600" />

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="address"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Address') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="address" id="address" placeholder="Bobcat Lane"
                                        autocomplete="address"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="city"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('City') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="city" id="city" placeholder="St. Robert"
                                        autocomplete="city"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="state"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('State') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="state" id="state" placeholder="Missouri"
                                        autocomplete="state"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="zip"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Zip') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="zip" id="zip" placeholder="1234 NW"
                                        autocomplete="zip"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="country"
                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                    {{ __('Country') }}
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="country" id="country" placeholder="United States"
                                        autocomplete="country"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm dark:bg-darkmode rounded-md">
                                </div>
                            </div>
                        </div>

                        <hr class="my-6 border-b-1 border-gray-300 dark:border-gray-600" />

                        <div class="mt-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
