<x-admin-layout>
    <x-slot name="title">
        {{ __('Settings') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-lg">
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
                        <div class="p-6">
                            <div class="flex items-center">
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
                                                if ($(this).is(':checked')) {
                                                    $('#sitekey').removeClass('hidden');
                                                    $('#secretkey').removeClass('hidden');
                                                } else {
                                                    $('#sitekey').addClass('hidden');
                                                    $('#secretkey').addClass('hidden');
                                                }
                                            });
                                        });
                                    </script>

                                </div>
                            </div>
                            <br><br>
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
</x-admin-layout>
