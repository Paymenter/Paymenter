<x-guest-layout>
    <div class="flex justify-center items-center h-screen">


        <div
            class="flex w-full max-w-sm mx-auto overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800 lg:max-w-4xl">
            <div class="hidden bg-cover lg:block lg:w-1/2"
                style="background-image: url('{{ asset(config('settings::app_logo')) }}');"></div>

            <form class="w-full px-6 py-8 md:px-8 lg:w-1/2" method="POST" action="{{ route('login') }}">
                @csrf
                <h2 class="text-2xl font-semibold text-center text-gray-700 dark:text-white">
                    {{ config('settings::app_name') }}
                </h2>

                <p class="text-xl text-center text-gray-600 dark:text-gray-200">
                    Welcome back!
                </p>

                @if (config('settings::discord_enabled'))
                <a href="{{ route('social.login', 'discord') }}"
                    class="flex items-center justify-center mt-4 text-gray-600 transition-colors duration-300 transform border rounded-lg dark:border-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <div class="px-4 py-2">
                        <i class="fa-brands fa-discord"></i>
                    </div>

                    <span class="w-5/6 px-4 py-3 font-bold text-center">Sign in with Discord</span>
                </a>
                @endif

                <div class="flex items-center justify-between mt-4">
                    <span class="w-1/5 border-b dark:border-gray-600 lg:w-1/4"></span>

                    <span
                        class="text-xs text-center text-gray-500 uppercase dark:text-gray-400">or login
                        with email</span>

                    <span class="w-1/5 border-b dark:border-gray-400 lg:w-1/4"></span>
                </div>

                <div class="mt-4">
                    <label class="block mb-2 text-sm font-medium text-gray-600 dark:text-gray-200"
                        for="LoggingEmailAddress">Email Address</label>
                    <input id="LoggingEmailAddress"
                        class="block w-full px-4 py-2 text-gray-700 bg-white border rounded-lg dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-opacity-40 dark:focus:border-blue-300 focus:outline-none focus:ring focus:ring-blue-300"
                        type="email" name="email" />
                </div>

                <div class="mt-4">
                    <div class="flex justify-between">
                        <label class="block mb-2 text-sm font-medium text-gray-600 dark:text-gray-200"
                            for="loggingPassword">Password</label>
                        <a href="#" class="text-xs text-gray-500 dark:text-gray-300 hover:underline">Forget
                            Password?</a>
                    </div>

                    <input id="loggingPassword"
                        class="block w-full px-4 py-2 text-gray-700 bg-white border rounded-lg dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-400 focus:ring-opacity-40 dark:focus:border-blue-300 focus:outline-none focus:ring focus:ring-blue-300"
                        type="password" name="password" />
                </div>
                @if (config('settings::recaptcha') == 1)
                    <div class="flex items-center justify-center mt-1">
                        <div class="g-recaptcha" data-sitekey="{{ config('settings::recaptcha_site_key') }}"></div>
                    </div>
                @endif
                <div class="mt-6">
                    <button type="submit"
                        class="w-full px-6 py-3 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-gray-800 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring focus:ring-gray-300 focus:ring-opacity-50">
                        Sign In
                    </button>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <span class="w-1/5 border-b dark:border-gray-600 md:w-1/4"></span>

                    <a href="#" class="text-xs text-gray-500 uppercase dark:text-gray-400 hover:underline">or sign
                        up</a>

                    <span class="w-1/5 border-b dark:border-gray-600 md:w-1/4"></span>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
