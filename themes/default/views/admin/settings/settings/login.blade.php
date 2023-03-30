<div class="hidden mt-3" id="tab-login">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.login') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- discord -->
            <h2 class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext flex items-center space-x-1">
                <span>{{ __('Discord:') }} </span>
                <svg width="16" height="16"
                    class="w-5 h-5 mr-1 text-gray-400 transition duration-150 ease-in-out cursor-help fill-current dark:text-darkmodetext"
                    data-popover-target="discord" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"></path>
                </svg>
                <div id="discord" role="tooltip" data-popover
                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    {{ __('Discord OAuth2 is a service that allows users to log in to your site using their Discord account.') }}
                    <br />
                    {{ __('You need to set your redirect url to') }}
                    <span class="text-blue-500 hover:cursor-pointer	"
                        onclick="copyToClipboard('{{ route('social.login', 'discord') }}')">
                        {{ route('social.login', 'discord') }}</span>
                    <div data-popper-arrow></div>
                </div>
                </span>
            </h2>
            <!-- enable discord -->
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('seo_twitter_card') is-invalid @enderror"
                    placeholder=" " name="discord_enabled" value="1"
                    {{ config('settings::discord_enabled') ? 'checked' : '' }} />
                <label class="form-label" style="position: unset;">{{ __('Enable Discord') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('discord_client_id') is-invalid @enderror"
                    placeholder=" " name="discord_client_id" value="{{ config('services.discord.client_id') }}" />
                <label class="form-label">{{ __('Discord Client ID') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('discord_client_secret') is-invalid @enderror"
                    placeholder=" " name="discord_client_secret"
                    value="{{ config('services.discord.client_secret') }}" />
                <label class="form-label">{{ __('Discord Client Secret') }}</label>
            </div>

            <h2 class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext flex items-center space-x-1">
                <span>{{ __('Google:') }} </span>
                <svg width="16" height="16"
                    class="w-5 h-5 mr-1 text-gray-400 transition duration-150 ease-in-out cursor-help fill-current dark:text-darkmodetext"
                    data-popover-target="google" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"></path>
                </svg>
                <div id="google" role="tooltip" data-popover
                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    {{ __('Google OAuth2 is a service that allows users to log in to your site using their Google account.') }}
                    <br />
                    {{ __('You need to set your redirect url to') }}
                    <span class="text-blue-500 hover:cursor-pointer	"
                        onclick="copyToClipboard('{{ route('social.login', 'google') }}')">
                        {{ route('social.login', 'google') }}</span>
                    <div data-popper-arrow></div>
                </div>
                </span>
            </h2>
            <!-- enable google -->
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('seo_twitter_card') is-invalid @enderror"
                    placeholder=" " name="google_enabled" value="1"
                    {{ config('settings::google_enabled') ? 'checked' : '' }} />
                <label class="form-label" style="position: unset;">{{ __('Enable Google') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('google_client_id') is-invalid @enderror"
                    placeholder=" " name="google_client_id" value="{{ config('settings::google_client_id') }}" />
                <label class="form-label">{{ __('Google Client ID') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('google_client_secret') is-invalid @enderror"
                    placeholder=" " name="google_client_secret"
                    value="{{ config('settings::google_client_secret') }}" />
                <label class="form-label">{{ __('Google Client Secret') }}</label>
            </div>

            <!-- Github -->
            <h2 class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext flex items-center space-x-1">
                <span>{{ __('Github:') }} </span>
                <svg width="16" height="16"
                    class="w-5 h-5 mr-1 text-gray-400 transition duration-150 ease-in-out cursor-help fill-current dark:text-darkmodetext"
                    data-popover-target="github" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"></path>
                </svg>
                <div id="github" role="tooltip" data-popover
                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    {{ __('Github OAuth2 is a service that allows users to log in to your site using their Github account.') }}
                    <br />
                    {{ __('You need to set your redirect url to') }}
                    <span class="text-blue-500 hover:cursor-pointer	"
                        onclick="copyToClipboard('{{ route('social.login', 'github') }}')">
                        {{ route('social.login', 'github') }}</span>
                    <div data-popper-arrow></div>
                </div>
                </span>
            </h2>
            <!-- enable github -->
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('seo_twitter_card') is-invalid @enderror"
                    placeholder=" " name="github_enabled" value="1"
                    {{ config('settings::github_enabled') ? 'checked' : '' }} />
                <label class="form-label" style="position: unset;">{{ __('Enable Github') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('github_client_id') is-invalid @enderror"
                    placeholder=" " name="github_client_id" value="{{ config('settings::github_client_id') }}" />
                <label class="form-label">{{ __('Github Client ID') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('github_client_secret') is-invalid @enderror"
                    placeholder=" " name="github_client_secret"
                    value="{{ config('settings::github_client_secret') }}" />
                <label class="form-label">{{ __('Github Client Secret') }}</label>
            </div>

        </div>
        <button class="form-submit float-right">{{ __('Submit') }}</button>
    </form>
</div>
