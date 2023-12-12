<div class="hidden mt-3" id="tab-login">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.login') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- discord -->
            <h2 class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext flex items-center space-x-1">
                <span>{{ __('Discord:') }} </span>
                <svg width="16" height="16" class="w-5 h-5 mr-1 text-gray-400 transition duration-150 ease-in-out cursor-help fill-current dark:text-darkmodetext" data-popover-target="discord" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                </svg>
                <div id="discord" role="tooltip" data-popover class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    {{ __('Discord OAuth2 is a service that allows users to log in to your site using their Discord account.') }}
                    <br />
                    {{ __('You need to set your redirect url to') }}
                    <span class="text-blue-500 hover:cursor-pointer	" onclick="copyToClipboard('{{ route('social.login.callback', 'discord') }}')">
                        {{ route('social.login.callback', 'discord') }}</span>
                    <div data-popper-arrow></div>
                </div>
                </span>
            </h2>
            <!-- enable discord -->

            <x-input type="checkbox" class="mt-5 m-4" name="discord_enabled" value="1" :checked="config('settings::discord_enabled') == 1 " :label="__('Enable Discord')" />

            <x-input type="text" class="m-4" name="discord_client_id" :value="config('services.discord.client_id')" :label="__('Discord Client ID')" />

            <x-input type="text" class="m-4" name="discord_client_secret" :value="config('services.discord.client_secret')" :label="__('Discord Client Secret')" />


            <h2 class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext flex items-center space-x-1">
                <span>{{ __('Google:') }} </span>
                <svg width="16" height="16" class="w-5 h-5 mr-1 text-gray-400 transition duration-150 ease-in-out cursor-help fill-current dark:text-darkmodetext" data-popover-target="google" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                </svg>
                <div id="google" role="tooltip" data-popover class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    {{ __('Google OAuth2 is a service that allows users to log in to your site using their Google account.') }}
                    <br />
                    {{ __('You need to set your redirect url to') }}
                    <span class="text-blue-500 hover:cursor-pointer	" onclick="copyToClipboard('{{ route('social.login.callback', 'google') }}')">
                        {{ route('social.login.callback', 'google') }}</span>
                    <div data-popper-arrow></div>
                </div>
                </span>
            </h2>
            <!-- enable google -->

            <x-input type="checkbox" class="mb-5 m-4" name="google_enabled" value="1" :checked="config('settings::google_enabled') == 1" :label="__('Enable Google')" />

            <x-input type="text" class="m-4" name="google_client_id" :value="config('settings::google_client_id')" :label="__('Google Client ID')" />

            <x-input type="text" class="m-4" name="google_client_secret" :value="config('settings::google_client_secret')" :label="__('Google Client Secret')" />


            <!-- Github -->
            <h2 class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext flex items-center space-x-1">
                <span>{{ __('Github:') }} </span>
                <svg width="16" height="16" class="w-5 h-5 mr-1 text-gray-400 transition duration-150 ease-in-out cursor-help fill-current dark:text-darkmodetext" data-popover-target="github" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                </svg>
                <div id="github" role="tooltip" data-popover class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    {{ __('Github OAuth2 is a service that allows users to log in to your site using their Github account.') }}
                    <br />
                    {{ __('You need to set your redirect url to') }}
                    <span class="text-blue-500 hover:cursor-pointer	" onclick="copyToClipboard('{{ route('social.login.callback', 'github') }}')">
                        {{ route('social.login.callback', 'github') }}</span>
                    <div data-popper-arrow></div>
                </div>
                </span>
            </h2>
            <!-- enable github -->

            <x-input type="checkbox" class="mb-5 m-4" name="github_enabled" value="1" :checked="config('settings::github_enabled') == 1" :label="__('Enable Github')" />

            <x-input type="text" class="m-4" name="github_client_id" :value="config('settings::github_client_id')" :label="__('Github Client ID')" />

            <x-input type="text" class="m-4" name="github_client_secret" :value="config('settings::github_client_secret')" :label="__('Github Client Secret')" />
            
            <h1 class=" ml-6 text-xl col-span-2">The abillity to disable the registration of a new account.</h1>
            <div class="relative m-4 group">
                <x-input type="hidden" value="0" name="registrationAbillity_disable" />
                <x-input type="checkbox" name="registrationAbillity_disable" value="1" :checked="config('settings::registrationAbillity_disable') == 1" :label="__('Disable')" />
            </div>

            <h1 class=" ml-6 text-xl col-span-2">Set your required information before being able to purchase any products.</h1>
            <div class="relative m-4 group">
                <x-input type="hidden" value="0" name="requiredClientDetails_address" />
                <x-input type="checkbox" name="requiredClientDetails_address" value="1" :checked="config('settings::requiredClientDetails_address') == 1" :label="__('Address')" />
            </div>
            <div class="relative m-4 group">
                <x-input type="hidden" value="0" name="requiredClientDetails_city" />
                <x-input type="checkbox" name="requiredClientDetails_city" value="1" :checked="config('settings::requiredClientDetails_city') == 1" :label="__('City')" />
            </div>

            <div class="relative m-4 group">
                <x-input type="hidden" value="0" name="requiredClientDetails_zip" />
                <x-input type="checkbox" name="requiredClientDetails_zip" value="1" :checked="config('settings::requiredClientDetails_zip') == 1" :label="__('ZIP')" />
            </div>

            <div class="relative m-4 group">
                <x-input type="hidden" value="0" name="requiredClientDetails_country" />
                <x-input type="checkbox" name="requiredClientDetails_country" value="1" :checked="config('settings::requiredClientDetails_country') == 1" :label="__('Country')" />
            </div>

            <div class="relative m-4 group">
                <x-input type="hidden" value="0" name="requiredClientDetails_phone" />
                <x-input type="checkbox" name="requiredClientDetails_phone" value="1" :checked="config('settings::requiredClientDetails_phone') == 1" :label="__('Phone')" />
            </div>

        </div>

        <button class="form-submit float-right">{{ __('Submit') }}</button>
    </form>
</div>