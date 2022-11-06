<x-admin-layout>
    <x-slot name="title">
        {{ __('Settings') }}
    </x-slot>
    <div class="py-12">
        <div class="container mx-auto py-10 h-full px-6">
            <div class="w-full h-full rounded">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class=" dark:bg-darkmode2 bg-white overflow-hidden shadow-xl rounded-lg">
                        <div class="dark:bg-darkmode2 p-6 sm:px-20 bg-white">
                            <div class="dark:text-darkmodetext mt-8 text-2xl">
                                {{ __('Settings') }}
                            </div>

                            <div class="dark:text-darkmodetext mt-6 text-gray-500">
                                {{ __('Here you can change your settings.') }}
                            </div>
                        </div>
                        <x-success class="mb-4"/>
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <div class="dark:bg-darkmode2 bg-gray-200 bg-opacity-25 grid grid-cols-1">
                                <div class="p-6 divide-y divide-darkmode divide-y-8">
                                    <div class="flex flex-row items-center">
                                        <!-- Theme -->
                                        <div class="grow  mr-4 mb-4">
                                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">
                                                <div class="mt-4">
                                                    <label for="theme" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                        {{ __('Theme') }}
                                                    </label>
                                                    <div class="mt-1">
                                                        <select id="theme" name="theme"
                                                            class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full">
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
                                        <div class="ml-4 grow mt-4 mb-4">
                                            <label for="advanced_mode" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                {{ __('Advanced mode') }}
                                            </label>
                                            <div class="mt-1">
                                                <select id="advanced_mode" name="advanced_mode"
                                                    class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full">
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
                                        <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">                                
                                            <div class="mt-4">
                                                <label for="recaptcha" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                    {{ __('Google Recaptcha') }}
                                                </label>
                                                <div class="mt-1">
                                                    <!-- Boolean -->
                                                    <div class="flex items-center">
                                                        <input id="recaptcha" name="recaptcha" type="checkbox"
                                                            class="dark:bg-darkmode focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                            {{ $settings->recaptcha ? 'checked' : '' }}>
                                                        <label for="recaptcha"
                                                            class="dark:text-darkmodetext ml-3 block text-sm font-medium text-gray-700">
                                                            {{ __('Enable') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 hidden" id="sitekey">
                                                <label for="recaptcha_site_key" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                    {{ __('Recaptcha Site Key') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="recaptcha_site_key" name="recaptcha_site_key" type="text"
                                                        autocomplete="recaptcha_site_key"
                                                        class="dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full"
                                                        value="{{ $settings->recaptcha_site_key }}">
                                                </div>
                                            </div>
                                            <div class="mt-4 hidden" id="secretkey">
                                                <label for="recaptcha_secret_key"
                                                    class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                    {{ __('Recaptcha Secret Key') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="recaptcha_secret_key" name="recaptcha_secret_key" type="text"
                                                        autocomplete="recaptcha_secret_key"
                                                        class="dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full"
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
                                        <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">
                                            <div class="mt-4" id="seo_title">
                                                <label for="seo_title" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                    {{ __('SEO Title') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="seo_title" name="seo_title" type="text"
                                                        autocomplete="seo_title"
                                                        class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full"
                                                        value="{{ $settings->seo_title }}">
                                                </div>
                                            </div>
                                            <div class="mt-4" id="seo_description">
                                                <label for="seo_description" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                    {{ __('SEO Description') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="seo_description" name="seo_description" type="text"
                                                        autocomplete="seo_description"
                                                        class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full"
                                                        value="{{ $settings->seo_description }}">
                                                </div>
                                            </div>
                                            <div class="mt-4" id="seo_keywords">
                                                <label for="seo_keywords" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                    {{ __('SEO Keywords') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="seo_keywords" name="seo_keywords" type="text"
                                                        autocomplete="seo_keywords"
                                                        class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full"
                                                        value="{{ $settings->seo_keywords }}">
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <label for="seo_twitter_card" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                    {{ __('Twitter Card') }}
                                                </label>
                                                <div class="mt-1">
                                                    <div class="flex items-center">
                                                        <input id="seo_twitter_card" name="seo_twitter_card" type="checkbox"
                                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                            {{ $settings->seo_twitter_card ? 'checked' : '' }}>
                                                        <label for="seo_twitter_card"
                                                            class="dark:text-darkmodetext ml-3 block text-sm font-medium text-gray-700">
                                                            {{ __('Enable') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <label for="seo_image" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                    {{ __('SEO Image') }}
                                                </label>
                                                <div class="mt-1">
                                                    <input id="seo_image" name="seo_image" type="text"
                                                        autocomplete="seo_image"
                                                        class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full"
                                                        value="{{ $settings->seo_image }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br><br>
                                    @endif
                                    <div class=" dark:text-darkmodetext text-center text-3xl font-bold">{{ __('Personalization') }}</label>
                                        <div class="flex flex-row grow items-center">
                                            <div class="flex flex-col items-center mt-10">
                                                <div class="flex items-center">
                                                    <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">
                                                    <div class="mt-4">
                                                            <label for="navbar" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                                {{ __('Navbar Location') }}
                                                            </label>
                                                            <div class="mt-1">
                                                                <select id="navbar" name="navbar"
                                                                    class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full">
                                                                        <option value="1">Topbar</option>
                                                                        <option value="0">Sidebar</option>
                                                                </select>
                                                                <script>
                                                                    document.getElementById('navbar').value = "{{ $settings->navbar }}";
                                                                </script>
                                                            </div>
                                                        </div>
                                                        <div class="mt-4">
                                                            <label for="currency" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                                {{ __('Currency') }}
                                                            </label>
                                                            <div class="mt-1">
                                                                <input id="currency" name="currency_sign" type="text"
                                                                    autocomplete="currency"
                                                                    class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full"
                                                                    value="{{ $settings->currency_sign }}">
                                                            </div>
                                                        </div>
                                                        <div class="mt-4">
                                                            <label for="currency_position" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                                {{ __('Currency Position') }}
                                                            </label>
                                                            <div class="mt-1">
                                                                <select id="currency_position" name="currency_position"
                                                                    class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full">
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
                                            <div class="grow flex flex-col">
                                                <div class="grow flex flex-row items-center">
                                                    <div class="grow flex items-center">
                                                        <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">
                                                            <div class="mt-4 " id="homepage_text">
                                                                <label for="homepage_text" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                                    {{ __('App Name') }}
                                                                </label>
                                                                <div class="mt-1">
                                                                    <textarea id="home_page_text" name="home_page_text" type="text"
                                                                        autocomplete="homepage_text"
                                                                        class="dark:text-darkmodetext text-center dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full"
                                                                        rows="1">{{ $settings->app_name }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="grow flex flex-row items-center">
                                                    <div class="grow flex items-center">
                                                        <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold w-full">
                                                            <div class="mt-4 " id="homepage_text">
                                                                <label for="homepage_text" class="dark:text-darkmodetext block text-sm font-medium text-gray-700">
                                                                    {{ __('Homepage Text') }}
                                                                </label>
                                                                <div class="mt-1">
                                                                    <textarea id="home_page_text" name="home_page_text" type="text"
                                                                        autocomplete="homepage_text"
                                                                        class="dark:text-darkmodetext dark:bg-darkmode form-input rounded-md shadow-sm mt-1 block w-full"
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
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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
