<x-app-layout>

    <div class="content min-h-[50vh] flex items-center justify-center flex-col">
        <div class="flex items-center text-secondary-900 font-semibold text-lg py-4 gap-x-2">
            <x-application-logo class="w-10" />
            {{ config('app.name', 'Paymenter') }}
        </div>

        <div class="content-box max-w-lg w-full">
            <form method="POST" action="{{ route('login') }}" id="login">
                @csrf
                <h2 class="text-lg font-semibold">{{ __('Login to continue') }}</h2>
                <x-input class="mt-3" label="{{ __('Email') }}" type="email" placeholder="{{ __('Email..') }}" required
                    name="email" id="email" icon="ri-at-line" />
                    
                <div class="flex justify-between mt-4 mb-1 text-sm text-secondary-600" >
                    <label for="password">{{ __('Password') }}</label>
                    <a href="{{ route('password.request') }}" class="underline">{{ __('Forgot Password?') }}</a>
                </div>
                <x-input type="password" required
                    placeholder="{{ __('Password..') }}" name="password" id="password" icon="ri-lock-line"/>
                    
                <x-input type="checkbox" name="remember" id="remember" label="Remember me" class="mt-4" />
                
                <button class="button button-primary w-full mt-4">{{ __('Login') }}</button>

                <a href="{{ route('register') }}" class="text-sm text-secondary-600 underline mt-2 block text-center">{{ __('New here? Create an account.') }}</a>

                <div class="flex items-center justify-center">
                    <!-- Recaptcha, also send the form id -->
                    <x-recaptcha form="login" />
                </div>

                @if (config('settings::discord_enabled') == 1 ||
                        config('settings::apple_enabled') == 1 ||
                        config('settings::google_enabled') == 1 ||
                        config('settings::github_enabled') == 1)
                    <div class="flex items-center my-2">
                        <div class="w-full h-0.5 bg-secondary-200 dark:bg-secondary-300"></div>
                        <div class="px-5 text-center text-secondary-500 dark:text-secondary-400">{{ __('or') }}</div>
                        <div class="w-full h-0.5 bg-secondary-200 dark:bg-secondary-300"></div>
                    </div>
                    <div class="space-y-2">
                        @if (config('settings::google_enabled') == 1)
                            <a href="{{ route('social.login', 'google') }}"
                                class="button button-secondary !flex gap-x-2 items-center justify-center">
                                <svg class="w-5 h-5 mr-2" viewBox="0 0 21 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_13183_10121)">
                                        <path
                                            d="M20.3081 10.2303C20.3081 9.55056 20.253 8.86711 20.1354 8.19836H10.7031V12.0492H16.1046C15.8804 13.2911 15.1602 14.3898 14.1057 15.0879V17.5866H17.3282C19.2205 15.8449 20.3081 13.2728 20.3081 10.2303Z"
                                            fill="#3F83F8" />
                                        <path
                                            d="M10.7019 20.0006C13.3989 20.0006 15.6734 19.1151 17.3306 17.5865L14.1081 15.0879C13.2115 15.6979 12.0541 16.0433 10.7056 16.0433C8.09669 16.0433 5.88468 14.2832 5.091 11.9169H1.76562V14.4927C3.46322 17.8695 6.92087 20.0006 10.7019 20.0006V20.0006Z"
                                            fill="#34A853" />
                                        <path
                                            d="M5.08857 11.9169C4.66969 10.6749 4.66969 9.33008 5.08857 8.08811V5.51233H1.76688C0.348541 8.33798 0.348541 11.667 1.76688 14.4927L5.08857 11.9169V11.9169Z"
                                            fill="#FBBC04" />
                                        <path
                                            d="M10.7019 3.95805C12.1276 3.936 13.5055 4.47247 14.538 5.45722L17.393 2.60218C15.5852 0.904587 13.1858 -0.0287217 10.7019 0.000673888C6.92087 0.000673888 3.46322 2.13185 1.76562 5.51234L5.08732 8.08813C5.87733 5.71811 8.09302 3.95805 10.7019 3.95805V3.95805Z"
                                            fill="#EA4335" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_13183_10121">
                                            <rect width="20" height="20" fill="white"
                                                transform="translate(0.5)" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                {{ __('Sign in with Google') }}
                            </a>
                        @endif
                        @if (config('settings::apple_enabled'))
                            <a href="{{ route('social.login', 'apple') }}"
                                class="button button-secondary !flex gap-x-2 items-center justify-center">
                                <svg class="w-5 h-5 mr-2 text-secondary-900 dark:text-white" viewBox="0 0 21 20"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_13183_29163)">
                                        <path
                                            d="M18.6574 15.5863C18.3549 16.2851 17.9969 16.9283 17.5821 17.5196C17.0167 18.3257 16.5537 18.8838 16.1969 19.1936C15.6439 19.7022 15.0513 19.9627 14.4168 19.9775C13.9612 19.9775 13.4119 19.8479 12.7724 19.585C12.1308 19.3232 11.5412 19.1936 11.0021 19.1936C10.4366 19.1936 9.83024 19.3232 9.18162 19.585C8.53201 19.8479 8.00869 19.985 7.60858 19.9985C7.00008 20.0245 6.39356 19.7566 5.78814 19.1936C5.40174 18.8566 4.91842 18.2788 4.33942 17.4603C3.71821 16.5863 3.20749 15.5727 2.80738 14.4172C2.37887 13.1691 2.16406 11.9605 2.16406 10.7904C2.16406 9.45009 2.45368 8.29407 3.03379 7.32534C3.4897 6.54721 4.09622 5.9334 4.85533 5.4828C5.61445 5.03219 6.43467 4.80257 7.31797 4.78788C7.80129 4.78788 8.4351 4.93738 9.22273 5.2312C10.0081 5.52601 10.5124 5.67551 10.7335 5.67551C10.8988 5.67551 11.4591 5.5007 12.4088 5.15219C13.3069 4.82899 14.0649 4.69517 14.6859 4.74788C16.3685 4.88368 17.6327 5.54699 18.4734 6.74202C16.9685 7.65384 16.2241 8.93097 16.2389 10.5693C16.2525 11.8454 16.7154 12.9074 17.6253 13.7506C18.0376 14.1419 18.4981 14.4444 19.0104 14.6592C18.8993 14.9814 18.7821 15.29 18.6574 15.5863V15.5863ZM14.7982 0.400358C14.7982 1.40059 14.4328 2.3345 13.7044 3.19892C12.8254 4.22654 11.7623 4.82035 10.6093 4.72665C10.5947 4.60665 10.5861 4.48036 10.5861 4.34765C10.5861 3.38743 11.0041 2.3598 11.7465 1.51958C12.1171 1.09416 12.5884 0.740434 13.16 0.458257C13.7304 0.18029 14.2698 0.0265683 14.7772 0.000244141C14.7921 0.133959 14.7982 0.267682 14.7982 0.400345V0.400358Z"
                                            fill="currentColor" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_13183_29163">
                                            <rect width="20" height="20" fill="white"
                                                transform="translate(0.5)" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                {{ __('Sign in with Apple') }}
                            </a>
                        @endif
                        @if (config('settings::discord_enabled'))
                            <a href="{{ route('social.login', 'discord') }}"
                                class="button button-secondary !flex gap-x-2 items-center justify-center">
                                <svg class="w-5 h-5 mr-2 text-secondary-900 dark:text-secondary" viewBox="0 0 127.14 96.36"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <style>
                                            .cls-1 {
                                                fill: var(--secondary-900);
                                            }
                                        </style>
                                    </defs>

                                    <g id="Discord_Logo_-_Large_-_White" data-name="Discord Logo - Large - White">
                                        <path class="cls-1"
                                            d="M107.7,8.07A105.15,105.15,0,0,0,81.47,0a72.06,72.06,0,0,0-3.36,6.83A97.68,97.68,0,0,0,49,6.83,72.37,72.37,0,0,0,45.64,0,105.89,105.89,0,0,0,19.39,8.09C2.79,32.65-1.71,56.6.54,80.21h0A105.73,105.73,0,0,0,32.71,96.36,77.7,77.7,0,0,0,39.6,85.25a68.42,68.42,0,0,1-10.85-5.18c.91-.66,1.8-1.34,2.66-2a75.57,75.57,0,0,0,64.32,0c.87.71,1.76,1.39,2.66,2a68.68,68.68,0,0,1-10.87,5.19,77,77,0,0,0,6.89,11.1A105.25,105.25,0,0,0,126.6,80.22h0C129.24,52.84,122.09,29.11,107.7,8.07ZM42.45,65.69C36.18,65.69,31,60,31,53s5-12.74,11.43-12.74S54,46,53.89,53,48.84,65.69,42.45,65.69Zm42.24,0C78.41,65.69,73.25,60,73.25,53s5-12.74,11.44-12.74S96.23,46,96.12,53,91.08,65.69,84.69,65.69Z" />
                                    </g>

                                </svg>
                                {{ __('Sign in with Discord') }}
                            </a>
                        @endif
                        @if (config('settings::github_enabled'))
                            <a href="{{ route('social.login', 'github') }}"
                                class="button button-secondary !flex gap-x-2 items-center justify-center">
                                <svg class="w-5 h-5 mr-2 text-secondary-900 dark:text-white" viewBox="0 0 20 20"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10 0C4.477 0 0 4.477 0 10C0 14.92 3.59 18.89 8.21 19.81C8.83 19.86 9.02 19.47 9.02 19.12C9.02 18.77 9.01 17.92 9.01 16.76C5.83 17.45 5.37 15.79 5.37 15.79C4.96 14.94 4.34 14.5 4.34 14.5C3.62 13.86 4.5 13.88 4.5 13.88C5.3 13.95 5.83 14.73 5.83 14.73C6.74 16.04 8.34 15.6 9.05 15.38C9.16 15.08 9.34 14.72 9.55 14.4C7.18 14.14 4.67 13.42 4.67 9.58C4.67 8.62 5.07 7.82 5.68 7.17C5.54 6.92 5.14 6.09 5.82 4.97C5.82 4.97 6.63 4.74 8.23 5.71C9.1 5.42 10.06 5.28 11.02 5.27C11.98 5.28 12.94 5.42 13.81 5.71C15.41 4.74 16.22 4.97 16.22 4.97C16.9 6.09 16.5 6.92 16.36 7.17C16.97 7.82 17.37 8.62 17.37 9.58C17.37 13.42 14.86 14.14 12.49 14.4C12.7 14.72 12.88 15.08 12.99 15.38C13.7 15.6 15.3 16.04 16.21 14.73C16.21 14.73 16.74 13.95 17.54 13.88C17.54 13.88 18.42 13.86 17.7 14.5C17.7 14.5 17.08 14.94 16.67 15.79C16.67 15.79 16.21 17.45 13.03 16.76C13.03 17.92 13.02 18.77 13.02 19.12C13.02 19.47 13.21 19.86 13.83 19.81C18.41 18.89 22 14.92 22 10C22 4.477 17.523 0 12 0H10Z"
                                        fill="currentColor" />
                                </svg>
                                {{ __('Sign in with GitHub') }}
                            </a>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>

</x-app-layout>