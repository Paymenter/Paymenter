<x-admin-layout>
    <x-slot name="title">
        {{ __('Settings') }}
    </x-slot>
    <div class="py-12">
        <div class="container h-full px-6 py-10 mx-auto">
            <div class="w-full h-full rounded">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="overflow-hidden bg-white rounded-lg shadow-xl dark:bg-darkmode2">
                        <div class="p-6 bg-white dark:bg-darkmode2 sm:px-20">
                            <div class="mt-8 text-2xl dark:text-darkmodetext">
                                {{ __('Settings') }}
                            </div>

                            <div class="mt-6 text-gray-500 dark:text-darkmodetext">
                                {{ __('Here you can change your settings.') }}
                            </div>
                        </div>
                        <x-success class="mb-4"/>
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 bg-gray-200 bg-opacity-25 dark:bg-darkmode2">
                                <div class="p-6 divide-y-8 divide-y divide-darkmode">
                                    <div class="flex flex-row items-center">
                                        <!-- Theme -->
                                        <div class="mb-4 mr-4 grow">
                                            <div class="w-full ml-4 text-lg font-semibold leading-7 text-gray-600">
                                                <div class="mt-4">
                                                    <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                        {{ __('Theme') }}
                                                    </label>
                                                    <div class="mt-1">
                                                        <select id="theme" name="theme"
                                                            class="block w-full mt-1 rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input">
                                                            @foreach ($themes as $theme)
                                                                <option value="{{ $theme }}"
                                                                    @if ($theme == $currentTheme) selected @endif>
                                                                    {{ $theme }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Advanced Mode -->
                                        <div class="mt-4 mb-4 ml-4 grow">
                                            <label for="advanced_mode" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                {{ __('Advanced mode') }}
                                            </label>
                                            <div class="mt-1">
                                                <select id="advanced_mode" name="advanced_mode"
                                                    class="block w-full mt-1 rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input">
                                                        <option value="1" 
                                                            @if ($settings->advanced_mode == '1' ) {{"selected"}} @endif >
                                                            Enable</option>
                                                        <option value="0">Disable</option>
                                                </select>
                                                <script>
                                                    document.getElementById("advanced_mode").value = "{{$settings->advanced_mode}}";
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- security -->
                                    <div class="flex items-center">
                                        <div class="w-full ml-4 text-lg font-semibold leading-7 text-gray-600">                                
                                            <div class="mt-4">
                                                <label for="recaptcha" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                    {{ __('Google Recaptcha') }}
                                                </label>
                                                <div class="mt-1">
                                                    <!-- Boolean -->
                                                    <div class="flex items-center">
                                                        <input id="recaptcha" name="recaptcha" type="checkbox"
                                                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded dark:bg-darkmode focus:ring-indigo-500"
                                                            {{ $settings->recaptcha ? 'checked' : '' }}>
                                                        <label for="recaptcha"
                                                            class="block ml-3 text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                            {{ __('Enable') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hidden mt-4" id="sitekey">
                                                <label for="recaptcha_site_key" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                    {{ __('Recaptcha Site Key') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="recaptcha_site_key" name="recaptcha_site_key" type="text"
                                                        autocomplete="recaptcha_site_key"
                                                        class="block w-full mt-1 rounded-md shadow-sm dark:bg-darkmode form-input"
                                                        value="{{ $settings->recaptcha_site_key }}">
                                                </div>
                                            </div>
                                            <div class="hidden mt-4" id="secretkey">
                                                <label for="recaptcha_secret_key"
                                                    class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                    {{ __('Recaptcha Secret Key') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="recaptcha_secret_key" name="recaptcha_secret_key" type="text"
                                                        autocomplete="recaptcha_secret_key"
                                                        class="block w-full mt-1 rounded-md shadow-sm dark:bg-darkmode form-input"
                                                        value="{{ $settings->recaptcha_secret_key }}">
                                                </div>
                                            </div>
                                            
                                            <script>
                                            $(document).ready(function() {
                                                if ($('#recaptcha').is(':checked')) {
                                                    $('#sitekey').removeClass('hidden');
                                                    $('#secretkey').removeClass('hidden');
                                                }
                                                $('#recaptcha').click(function() {
                                                    $('#sitekey').toggleClass('hidden');
                                                    $('#secretkey').toggleClass('hidden');
                                                });
                                            });
                                            </script>

                                        </div>
                                    </div>
                                    <br><br>
                                    @if ($settings->advanced_mode)
                                    <div class="flex items-center">
                                        <div class="w-full ml-4 text-lg font-semibold leading-7 text-gray-600">
                                            <div class="mt-4" id="seo_title">
                                                <label for="seo_title" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                    {{ __('SEO Title') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="seo_title" name="seo_title" type="text"
                                                        autocomplete="seo_title"
                                                        class="block w-full mt-1 rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input"
                                                        value="{{ $settings->seo_title }}">
                                                </div>
                                            </div>
                                            <div class="mt-4" id="seo_description">
                                                <label for="seo_description" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                    {{ __('SEO Description') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="seo_description" name="seo_description" type="text"
                                                        autocomplete="seo_description"
                                                        class="block w-full mt-1 rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input"
                                                        value="{{ $settings->seo_description }}">
                                                </div>
                                            </div>
                                            <div class="mt-4" id="seo_keywords">
                                                <label for="seo_keywords" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                    {{ __('SEO Keywords') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="seo_keywords" name="seo_keywords" type="text"
                                                        autocomplete="seo_keywords"
                                                        class="block w-full mt-1 rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input"
                                                        value="{{ $settings->seo_keywords }}">
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <label for="seo_twitter_card" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                    {{ __('Twitter Card') }}
                                                </label>
                                                <div class="mt-1">
                                                    <div class="flex items-center">
                                                        <input id="seo_twitter_card" name="seo_twitter_card" type="checkbox"
                                                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                            {{ $settings->seo_twitter_card ? 'checked' : '' }}>
                                                        <label for="seo_twitter_card"
                                                            class="block ml-3 text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                            {{ __('Enable') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <label for="seo_image" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                    {{ __('SEO Image') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="seo_image" name="seo_image" type="text"
                                                        autocomplete="seo_image"
                                                        class="block w-full mt-1 rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input"
                                                        value="{{ $settings->seo_image }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br><br>
                                    @endif
                                    <div class="text-3xl font-bold text-center dark:text-darkmodetext">{{ __('Personalization') }}</label>
                                        <div class="flex flex-row items-center grow">
                                            <div class="flex flex-col items-center mt-10">
                                                <div class="flex items-center">
                                                    <div class="w-full ml-4 text-lg font-semibold leading-7 text-gray-600">
                                                    <div class="mt-4">
                                                            <label for="navbar" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                                {{ __('Navbar Location') }}
                                                            </label>
                                                            <div class="mt-1">
                                                                <select id="navbar" name="navbar"
                                                                    class="block w-full mt-1 rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input">
                                                                        <option value="1">Topbar</option>
                                                                        <option value="0">Sidebar</option>
                                                                </select>
                                                                <script>
                                                                    document.getElementById('navbar').value = "{{ $settings->sidebar }}";
                                                                </script>
                                                            </div>
                                                        </div>
                                                        <div class="mt-4">
                                                            <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                                {{ __('Currency') }}
                                                            </label>
                                                            <div class="mt-1">
                                                                <input id="currency" name="currency_sign" type="text"
                                                                    autocomplete="currency"
                                                                    class="block w-full mt-1 rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input"
                                                                    value="{{ $settings->currency_sign }}">
                                                            </div>
                                                        </div>
                                                        <div class="mt-4">1
                                                            <label for="currency_position" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                                {{ __('Currency Position') }}
                                                            </label>
                                                            <div class="mt-1">
                                                                <select id="currency_position" name="currency_position"
                                                                    class="block w-full mt-1 rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input">
                                                                        <option value="1">Left Side</option>
                                                                        <option value="0">Right Side</option>
                                                                </select>
                                                                <script>
                                                                    document.getElementById('currency_position').value = "{{ $settings->currency_position }}";
                                                                </script>
                                                            </div>
                                                        </div>
                                                        <br><br>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col grow">
                                                <div class="flex flex-row items-center grow">
                                                    <div class="flex items-center grow">
                                                        <div class="w-full ml-4 text-lg font-semibold leading-7 text-gray-600">
                                                            <div class="mt-4 " id="homepage_text">
                                                                <label for="homepage_text" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                                    {{ __('App Name') }}
                                                                </label>
                                                                <div class="mt-1">
                                                                    <textarea id="home_page_text" name="home_page_text" type="text"
                                                                        autocomplete="homepage_text"
                                                                        class="block w-full mt-1 text-center rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input"
                                                                        rows="1">{{ $settings->app_name }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex flex-row items-center grow">
                                                    <div class="flex items-center grow">
                                                        <div class="w-full ml-4 text-lg font-semibold leading-7 text-gray-600">
                                                            <div class="mt-4 " id="homepage_text">
                                                                <label for="homepage_text" class="block text-sm font-medium text-gray-700 dark:text-darkmodetext">
                                                                    {{ __('Homepage Text') }}
                                                                </label>
                                                                <div class="mt-1">
                                                                    <textarea id="home_page_text" name="home_page_text" type="text"
                                                                        autocomplete="homepage_text"
                                                                        class="block w-full mt-1 rounded-md shadow-sm dark:text-darkmodetext dark:bg-darkmode form-input"
                                                                        rows="5">{{ $settings->home_page_text }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="flex items-center justify-end mt-4">
                                        <button type="submit"
                                            class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                            {{ __('normal.edit') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
