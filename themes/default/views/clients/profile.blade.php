<x-app-layout title="{{ __('Edit profile') }}" clients>

    <x-success />

    <div class="content">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <div class="content-box">
                    <h2 class="text-xl font-semibold">{{ __('Profile Settings') }}</h2>
                </div>
            </div>
            <div class="lg:col-span-3 col-span-12">
                <div class="content-box">
                    <div class="flex gap-x-2 items-center">
                        <div
                            class="bg-primary-400 w-8 h-8 flex items-center justify-center rounded-md text-gray-50 text-xl">
                            <i class="ri-account-circle-line"></i>
                        </div>
                        <h3 class="font-semibold text-lg">{{ __('My Account') }}</h3>
                    </div>
                    <div class="flex flex-col gap-2 mt-2">
                        <a href="{{ route('clients.profile') }}"
                            class="text-secondary-900 pl-3 border-primary-400 border-l-2 duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                            {{ __('My Details') }}
                        </a>
                        @if (config('settings::credits'))
                            <a href="{{ route('clients.credits') }}"
                                class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                                {{ __('Credits') }}
                            </a>
                        @endif
                        <a href="{{ route('clients.api.index') }}"
                            class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                            {{ __('Account API') }}
                        </a>
                        @if (config('settings::affiliate'))
                            <a href="{{ route('clients.affiliate') }}"
                                class="border-l-2 border-transparent duration-300 hover:text-secondary-900 hover:pl-3 hover:border-primary-400 focus:text-secondary-900 focus:pl-3 focus:border-primary-400">
                                {{ __('Affiliate') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="lg:col-span-9 col-span-12">
                <div class="content-box">
                    <h1 class="text-xl font-semibold">{{ __('My Details') }}</h1>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="mt-6">
                            <form method="POST" action="{{ route('clients.profile.update') }}">
                                @csrf
                                <x-input type="text" class="mt-4" placeholder="{{ __('First name') }}"
                                    name="first_name" id="first_name" label="{{ __('First Name') }}"
                                    value="{{ Auth::user()->first_name }}" required/>
                                <x-input type="text" class="mt-4" placeholder="{{ __('Last name') }}"
                                    name="last_name" id="last_name" label="{{ __('Last Name') }}"
                                    value="{{ Auth::user()->last_name }}" required/>
                                <x-input type="text" class="mt-4" placeholder="{{ __('Address') }}" name="address"
                                    id="address" label="{{ __('Address') }}" value="{{ Auth::user()->address }}" :required="config('settings::requiredClientDetails_address') == 1"/>
                                <x-input type="text" class="mt-4" placeholder="{{ __('Zip') }}" name="zip"
                                    id="zip" label="{{ __('Zip') }}" value="{{ Auth::user()->zip }}" :required="config('settings::requiredClientDetails_zip') == 1" />
                                <x-input type="text" class="mt-4" placeholder="{{ __('City') }}" name="city"
                                    id="city" label="{{ __('City') }}" value="{{ Auth::user()->city }}" :required="config('settings::requiredClientDetails_city') == 1"/>
                                <x-input type="select" class="mt-4" placeholder="{{ __('Country') }}" name="country"
                                    id="country" label="{{ __('Country') }}" :required="config('settings::requiredClientDetails_country') == 1">
                                    @if(!config('settings::requiredClientDetails_country') == 1)
                                        <option value="">{{ __('Select a country') }}</option>
                                    @endif
                                    @foreach (App\Classes\Constants::countries() as $key => $country)
                                        <option value="{{ $key }}" @if (Auth::user()->country == $key) selected @endif>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </x-input>
                                <x-input type="text" class="mt-4" placeholder="{{ __('Phone') }}" name="phone"
                                    id="phone" label="{{ __('Phone') }}" value="{{ Auth::user()->phone }}" :required="config('settings::requiredClientDetails_phone') == 1"/>
                                <div class="flex justify-end mt-6">
                                    <button type="submit" class="button button-primary">
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="content-box mt-4">
                    <h1 class="text-xl font-semibold">{{ __('2FA') }}</h1>
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        {{ __('Two factor authentication adds an extra layer of security to your account. Once enabled, you will be prompted to enter a code from your authenticator app when logging in.') }}
                    </p>
                    @isset($secret)
                        <button data-modal-target="tfa" data-modal-toggle="tfa" class="button button-primary"
                            type="button">
                            {{ __('Setup Two Factor Authentication') }}
                        </button>
                        <div id="tfa" tabindex="-1" aria-hidden="true"
                            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] md:h-full">
                            <div class="relative w-full h-full max-w-2xl md:h-auto">
                                <div class="content-box">
                                    <div class="flex items-start justify-between p-4 border-b rounded-t">
                                        <h3 class="text-xl font-semibold text-secondary-900">
                                            {{ __('Two Factor Authentication') }}
                                        </h3>
                                        <button type="button"
                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                            data-modal-hide="tfa">
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
                                                <x-input id="code" label="{{ __('Code') }}" name="code"
                                                    required type="text" />
                                            </div>
                                            <div class="mt-4">
                                                <x-input id="password" label="{{ __('Password') }}" name="password"
                                                    required type="password" />
                                            </div>
                                            <div class="flex items-center justify-end mt-4">
                                                <button class="button button-primary" type="submit">
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
                            class="button button-danger"
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
                                            data-modal-hide="tfa">
                                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor"
                                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
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
                </div>
                <div class="content-box mt-4">
                    <h1 class="text-xl font-semibold">{{ __('Browser Sessions') }}</h1>
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        {{ __('Manage and logout your active sessions on other browsers and devices.') }}
                    </p>
                    <div class="flex flex-col gap-4 p-4">
                        @foreach (Auth::user()->sessions as $session)
                            <div class="flex items-center">
                                <div>
                                    @if($session->is_mobile)
                                        <i class="ri-smartphone-line text-2xl text-gray-400"></i>
                                    @else
                                        <i class="ri-computer-line text-2xl text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $session->formatted_device }}
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500">
                                            {{ $session->ip_address }},
                                            @if($session->is_current_device)
                                                <span class="text-green-500 font-semibold">This device</span>
                                            @else 
                                                {{ $session->formatted_last_active }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <form action="{{ route('clients.profile.sessions') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="button button-secondary">
                            {{ __('Logout Other Browser Sessions') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
