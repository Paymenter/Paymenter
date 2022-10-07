<x-admin-layout>
    <x-slot name="title">
        {{ __('Settings') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        {{ __('Settings') }}
                    </div>

                    <div class="mt-6 text-gray-500">
                        {{ __('Here you can change your settings.') }}
                    </div>
                </div>
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                    <div class="mt-4">
                                        <label for="theme" class="block text-sm font-medium text-gray-700">
                                            {{ __('Theme') }}
                                        </label>
                                        <div class="mt-1">
                                            <select id="theme" name="theme"
                                                class="form-input rounded-md shadow-sm mt-1 block w-full">
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
                                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                    <div class="mt-4">
                                        <label for="recaptcha" class="block text-sm font-medium text-gray-700">
                                            {{ __('Google Recaptcha') }}
                                        </label>
                                        <div class="mt-1">
                                            <!-- Boolean -->
                                            <div class="flex items-center">
                                                <input id="recaptcha" name="recaptcha" type="checkbox"
                                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                    {{ $settings->recaptcha ? 'checked' : '' }}>
                                                <label for="recaptcha"
                                                    class="ml-3 block text-sm font-medium text-gray-700">
                                                    {{ __('Enable') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 hidden" id="sitekey">
                                        <label for="recaptcha_site_key" class="block text-sm font-medium text-gray-700">
                                            {{ __('Recaptcha Site Key') }}
                                        </label>
                                        <div class="mt-1">
                                            <input id="recaptcha_site_key" name="recaptcha_site_key" type="text"
                                                autocomplete="recaptcha_site_key"
                                                class="form-input rounded-md shadow-sm mt-1 block w-full"
                                                value="{{ $settings->recaptcha_site_key }}">
                                        </div>
                                    </div>
                                    <div class="mt-4 hidden" id="secretkey">
                                        <label for="recaptcha_secret_key"
                                            class="block text-sm font-medium text-gray-700">
                                            {{ __('Recaptcha Secret Key') }}
                                        </label>
                                        <div class="mt-1">
                                            <input id="recaptcha_secret_key" name="recaptcha_secret_key" type="text"
                                                autocomplete="recaptcha_secret_key"
                                                class="form-input rounded-md shadow-sm mt-1 block w-full"
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
                        </div>
                </form>

            </div>
        </div>
    </div>
</x-admin-layout>
