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
    <div class="dark:bg-secondary-100 p-6 sm:px-20 bg-white">
        <div class="dark:text-darkmodetext mt-8 text-2xl">
            {{ __('Create client') }}
        </div>
        <div class="dark:text-darkmodetext mt-6 text-gray-500">
            {{ __('Here you can create a new client.') }}
        </div>
    </div>

    <div class="dark:bg-secondary-100 bg-gray-200 bg-opacity-25 grid grid-cols-1">
        <div class="p-6">
            <div class="flex items-center">
                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">
                    <form method="POST" action="{{ route('admin.clients.store') }}">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <x-input name="first_name" id="first_name" label="{{ __('First name') }}" type="text"
                                required placeholder="John" value="{{ old('first_name') }}" />

                            <x-input name="last_name" id="last_name" label="{{ __('Last name') }}" type="text"
                                required placeholder="Doe" value="{{ old('last_name') }}" />

                            <x-input name="email" id="email" label="{{ __('Email') }}" type="email" required
                                placeholder="john@doe.nl" value="{{ old('email') }}" />

                            <x-input name="phone" id="phone" label="{{ __('Phone') }}" type="text"
                                placeholder="+1234567890" value="{{ old('phone') }}" />

                            <x-input name="companyname" id="companyname" label="{{ __('Company Name') }}"
                                type="text" placeholder="Company Name" value="{{ old('companyname') }}" />

                            <x-input name="password" id="password" label="{{ __('Password') }}" type="password"
                                placeholder="********" value="{{ old('password') }}" />
                        </div>
                        <hr class="my-6 border-b-1 border-gray-300 dark:border-gray-600" />
                        <div class="grid grid-cols-3 gap-4">
                            <x-input name="address" id="address" label="{{ __('Address') }}" type="text"
                                placeholder="Bobcat Lane" value="{{ old('address') }}" />

                            <x-input name="city" id="city" label="{{ __('City') }}" type="text"
                                placeholder="St. Robert" value="{{ old('city') }}" />

                            <x-input name="state" id="state" label="{{ __('State') }}" type="text"
                                placeholder="Missouri" value="{{ old('state') }}" />
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <x-input name="country" id="country" label="{{ __('Country') }}" type="select"
                                placeholder="United States" value="{{ old('country') }}">
                                <option value="">{{ __('None') }}</option>
                                @foreach (App\Classes\Constants::countries() as $key => $country)
                                    <option value="{{ $key }}" @if (old('country') == $key) selected @endif>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </x-input>

                            <x-input name="zip" id="zip" label="{{ __('Zip') }}" type="text"
                                placeholder="1234 NW" value="{{ old('zip') }}" />
                        </div>

                        <hr class="my-6 border-b-1 border-gray-300 dark:border-gray-600" />

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit"
                                class="inline-flex justify-center w-max float-right button button-primary">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
