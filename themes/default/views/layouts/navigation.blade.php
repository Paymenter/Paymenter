<header class="w-full mt-4 md:px-16">
    <nav
        class="max-w-[1620px] mx-auto text-white block md:flex items-center gap-8 p-2 md:rounded-full bg-[#ffffff05] md:border-[1px] md:border-solid md:border-[#ffffff00] font-bold text-sm">
        <div class="flex justify-between items-center">
            <a href="{{ route('index') }}" class="w-fit flex shrink-0 p-4 text-sm font-bold">
                <!-- <x-application-logo class="w-10" />
                    {{ config('app.name', 'Paymenter') }} -->
                <div class="flex shrink-0 h-8">
                    <svg class="flex h-full w-full shrink-0" width="190" height="84" viewBox="0 0 190 84" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M28.0463 65.8185C28.0463 64.4823 28.0463 63.8142 28.1571 63.2586C28.612 60.9771 30.3999 59.1936 32.687 58.7398C33.2439 58.6293 33.9136 58.6293 35.2531 58.6293C36.5926 58.6293 37.2623 58.6293 37.8192 58.7398C40.1063 59.1936 41.8942 60.9771 42.3491 63.2586C42.4599 63.8142 42.4599 64.4823 42.4599 65.8185V68.215C42.4599 70.0067 42.4599 70.9026 42.0737 71.57C41.8207 72.0071 41.4567 72.3701 41.0185 72.6225C40.3496 73.0078 39.4515 73.0078 37.6554 73.0078H32.8508C31.0547 73.0078 30.1566 73.0078 29.4877 72.6225C29.0494 72.3701 28.6855 72.0071 28.4325 71.57C28.0463 70.9026 28.0463 70.0067 28.0463 68.215V65.8185Z"
                            fill="url(#paint0_radial_209_2)"></path>
                        <path
                            d="M28.0463 65.8185C28.0463 64.4823 28.0463 63.8142 28.1571 63.2586C28.612 60.9771 30.3999 59.1936 32.687 58.7398C33.2439 58.6293 33.9136 58.6293 35.2531 58.6293C36.5926 58.6293 37.2623 58.6293 37.8192 58.7398C40.1063 59.1936 41.8942 60.9771 42.3491 63.2586C42.4599 63.8142 42.4599 64.4823 42.4599 65.8185V68.215C42.4599 70.0067 42.4599 70.9026 42.0737 71.57C41.8207 72.0071 41.4567 72.3701 41.0185 72.6225C40.3496 73.0078 39.4515 73.0078 37.6554 73.0078H32.8508C31.0547 73.0078 30.1566 73.0078 29.4877 72.6225C29.0494 72.3701 28.6855 72.0071 28.4325 71.57C28.0463 70.9026 28.0463 70.0067 28.0463 68.215V65.8185Z"
                            fill="#F3B4A6" fill-opacity="0.06"></path>
                        <path
                            d="M17.2495 73.0078H22.1883C22.7475 73.0078 23.0271 73.0078 23.1713 72.8305C23.3155 72.6531 23.2551 72.3672 23.1343 71.7953C18.3784 49.2835 40.3702 54.3445 43.5542 38.7692C45.1793 30.8199 40.5536 20.1502 40.8267 12.7366C40.8609 11.8074 40.878 11.3428 40.6261 11.1988C40.3742 11.0548 40.0103 11.2858 39.2826 11.7479C29.0032 18.2751 21.5119 30.7526 24.2498 38.4081C24.6683 39.5784 24.8776 40.1635 24.6129 40.3772C24.3483 40.5909 23.9005 40.3154 23.0048 39.7645C21.5484 38.8687 19.8176 37.4022 18.6144 35.0915C18.2614 34.4136 18.0849 34.0747 17.8259 34.049C17.5669 34.0234 17.3444 34.2997 16.8994 34.8522C3.96515 50.9117 9.20659 66.9492 16.6769 72.8146C16.7961 72.9082 16.8557 72.955 16.9321 72.9814C17.0085 73.0078 17.0889 73.0078 17.2495 73.0078Z"
                            fill="url(#paint1_radial_209_2)"></path>
                        <path
                            d="M17.2495 73.0078H22.1883C22.7475 73.0078 23.0271 73.0078 23.1713 72.8305C23.3155 72.6531 23.2551 72.3672 23.1343 71.7953C18.3784 49.2835 40.3702 54.3445 43.5542 38.7692C45.1793 30.8199 40.5536 20.1502 40.8267 12.7366C40.8609 11.8074 40.878 11.3428 40.6261 11.1988C40.3742 11.0548 40.0103 11.2858 39.2826 11.7479C29.0032 18.2751 21.5119 30.7526 24.2498 38.4081C24.6683 39.5784 24.8776 40.1635 24.6129 40.3772C24.3483 40.5909 23.9005 40.3154 23.0048 39.7645C21.5484 38.8687 19.8176 37.4022 18.6144 35.0915C18.2614 34.4136 18.0849 34.0747 17.8259 34.049C17.5669 34.0234 17.3444 34.2997 16.8994 34.8522C3.96515 50.9117 9.20659 66.9492 16.6769 72.8146C16.7961 72.9082 16.8557 72.955 16.9321 72.9814C17.0085 73.0078 17.0889 73.0078 17.2495 73.0078Z"
                            fill="#F3B4A6" fill-opacity="0.06"></path>
                        <path
                            d="M49.4691 36.4952C50.1312 43.8733 45.681 48.9199 40.598 52.4158C39.7105 53.0261 39.2668 53.3313 39.3052 53.622C39.3435 53.9127 39.8738 54.1009 40.9344 54.4775C47.7119 56.8837 48.02 63.7812 47.4588 71.9766C47.4256 72.4612 47.409 72.7035 47.5515 72.8557C47.6939 73.0078 47.936 73.0078 48.4202 73.0078H52.6539C52.7821 73.0078 52.8462 73.0078 52.9083 72.9909C52.9705 72.974 53.0256 72.9416 53.1358 72.8767C68.3125 63.9468 60.1643 42.6148 50.9054 35.5735C50.2304 35.0601 49.8929 34.8034 49.6186 34.9545C49.3443 35.1055 49.3859 35.5687 49.4691 36.4952Z"
                            fill="url(#paint2_radial_209_2)"></path>
                        <path
                            d="M49.4691 36.4952C50.1312 43.8733 45.681 48.9199 40.598 52.4158C39.7105 53.0261 39.2668 53.3313 39.3052 53.622C39.3435 53.9127 39.8738 54.1009 40.9344 54.4775C47.7119 56.8837 48.02 63.7812 47.4588 71.9766C47.4256 72.4612 47.409 72.7035 47.5515 72.8557C47.6939 73.0078 47.936 73.0078 48.4202 73.0078H52.6539C52.7821 73.0078 52.8462 73.0078 52.9083 72.9909C52.9705 72.974 53.0256 72.9416 53.1358 72.8767C68.3125 63.9468 60.1643 42.6148 50.9054 35.5735C50.2304 35.0601 49.8929 34.8034 49.6186 34.9545C49.3443 35.1055 49.3859 35.5687 49.4691 36.4952Z"
                            fill="#F3B4A6" fill-opacity="0.06"></path>
                        <path
                            d="M81.1149 28.9825C84.0227 26.0779 87.5181 24.6256 91.6013 24.6256C95.6846 24.6256 99.1646 26.0779 102.041 28.9825C104.949 31.8562 106.403 35.3325 106.403 39.4113C106.403 43.4901 104.949 46.9818 102.041 49.8864C99.1646 52.7601 95.6846 54.197 91.6013 54.197C90.275 54.197 88.9797 54.0258 87.7157 53.6834C86.3874 53.3237 85.7233 53.1438 85.3679 53.4152C85.0125 53.6865 85.0125 54.3094 85.0125 55.5552V62.3098C85.0125 62.8628 85.0125 63.1393 84.8735 63.36C84.7344 63.5806 84.4847 63.7003 83.9854 63.9397L79.3946 66.1403C78.2291 66.699 77.6463 66.9783 77.223 66.7124C76.7997 66.4465 76.7997 65.8012 76.7997 64.5104V39.4113C76.7997 35.3325 78.2381 31.8562 81.1149 28.9825ZM96.2414 34.8226C94.9731 33.5248 93.4264 32.8759 91.6013 32.8759C89.7763 32.8759 88.2141 33.5248 86.9149 34.8226C85.6467 36.0895 85.0125 37.6191 85.0125 39.4113C85.0125 41.2344 85.6467 42.7949 86.9149 44.0927C88.2141 45.3596 89.7763 45.993 91.6013 45.993C93.4264 45.993 94.9731 45.3596 96.2414 44.0927C97.5406 42.7949 98.1902 41.2344 98.1902 39.4113C98.1902 37.6191 97.5406 36.0895 96.2414 34.8226Z"
                            fill="white"></path>
                        <path
                            d="M124.436 56.1437C123.353 59.0792 121.544 61.4121 119.007 63.1425C117.103 64.4811 115.056 65.32 112.865 65.6591C111.948 65.8012 111.489 65.8723 111.164 65.5495C110.839 65.2266 110.914 64.7284 111.065 63.7318L111.764 59.0946C111.845 58.557 111.886 58.2882 111.999 58.1199C112.112 57.9516 112.538 57.697 113.39 57.1878C113.915 56.874 114.253 56.4893 114.367 56.3754C114.563 56.18 116.223 54.5832 116.687 53.3163L117.163 52.0696C117.294 51.7242 117.36 51.5515 117.355 51.3718C117.35 51.192 117.274 51.0234 117.122 50.6863L106.557 27.1736C106.041 26.0252 105.783 25.451 106.05 25.0383C106.318 24.6256 106.948 24.6256 108.209 24.6256H113.218C113.798 24.6256 114.088 24.6256 114.315 24.7753C114.542 24.9249 114.655 25.1911 114.883 25.7236L119.623 36.8048C120.4 38.6221 120.788 39.5307 121.409 39.5088C122.03 39.4868 122.353 38.553 123 36.6853L126.752 25.8431C126.956 25.2551 127.057 24.9611 127.293 24.7934C127.529 24.6256 127.841 24.6256 128.464 24.6256H133.356C134.532 24.6256 135.121 24.6256 135.391 25.0113C135.662 25.3969 135.46 25.9489 135.057 27.0527L124.436 56.1437Z"
                            fill="white"></path>
                        <path
                            d="M135.587 39.4113C135.587 35.3325 137.025 31.8562 139.902 28.9825C142.28 26.607 145.998 25.2029 149.507 24.7702C150.509 24.6466 151.01 24.5849 151.326 24.9387C151.641 25.2926 151.514 25.815 151.261 26.8598L150.135 31.4941C149.992 32.0807 149.921 32.374 149.75 32.5507C149.578 32.7274 149.182 32.8371 148.391 33.0565C147.33 33.3505 146.517 33.985 145.679 34.8226C144.504 35.9949 144.625 37.6191 144.625 39.4113V53.0356C144.625 53.5996 144.625 53.8816 144.481 54.1049C144.338 54.3281 144.081 54.4454 143.567 54.6799L138.151 57.1519C136.996 57.6788 136.419 57.9423 136.003 57.6754C135.587 57.4086 135.587 56.7749 135.587 55.5076V39.4113Z"
                            fill="white"></path>
                        <path
                            d="M155.688 28.9825C158.596 26.0779 162.091 24.6256 166.174 24.6256C170.257 24.6256 173.737 26.0779 176.614 28.9825C179.522 31.8562 180.976 35.3325 180.976 39.4113C180.976 43.4901 179.522 46.9818 176.614 49.8864C173.737 52.7601 170.257 54.197 166.174 54.197C162.091 54.197 158.596 52.7601 155.688 49.8864C152.811 46.9818 151.373 43.4901 151.373 39.4113C151.373 35.3325 152.811 31.8562 155.688 28.9825ZM170.814 34.7763C169.546 33.4785 167.999 32.8296 166.174 32.8296C164.349 32.8296 162.787 33.4785 161.488 34.7763C160.22 36.0432 159.585 37.5882 159.585 39.4113C159.585 41.2035 160.22 42.7485 161.488 44.0463C162.787 45.3132 164.349 45.9467 166.174 45.9467C167.999 45.9467 169.546 45.3132 170.814 44.0463C172.113 42.7485 172.763 41.2035 172.763 39.4113C172.763 37.5882 172.113 36.0432 170.814 34.7763Z"
                            fill="white"></path>
                        <defs>
                            <radialGradient id="paint0_radial_209_2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse"
                                gradientTransform="translate(94.7049 19.4239) rotate(90) scale(67.5792 187.875)">
                                <stop stop-color="#FF343C"></stop>
                                <stop offset="1" stop-color="#F06F53"></stop>
                            </radialGradient>
                            <radialGradient id="paint1_radial_209_2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse"
                                gradientTransform="translate(94.7049 19.4239) rotate(90) scale(67.5792 187.875)">
                                <stop stop-color="#FF343C"></stop>
                                <stop offset="1" stop-color="#F06F53"></stop>
                            </radialGradient>
                            <radialGradient id="paint2_radial_209_2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse"
                                gradientTransform="translate(94.7049 19.4239) rotate(90) scale(67.5792 187.875)">
                                <stop stop-color="#FF343C"></stop>
                                <stop offset="1" stop-color="#F06F53"></stop>
                            </radialGradient>
                        </defs>
                    </svg>

                </div>
            </a>
            <!-- Mobile menu button -->
            <div class="flex md:hidden">
                <button type="button" class="button button-secondary-outline" onclick="openMobileMenu()">
                    <i class="ri-menu-line"></i>
                </button>
            </div>
            <script>
                function openMobileMenu() {
                    document.getElementById("mobile-menu").classList.toggle("hidden");
                    document.getElementById("clientsNavBar").classList.toggle("hidden");
                }
            </script>
        </div>
        <div class="w-full justify-between gap-4 md:flex hidden" id="mobile-menu">
            @auth
            <a href="{{ route('clients.home') }}"
                class="md:px-2 py-3 flex items-center gap-x-1 hover:text-secondary-800 duration-300">
                {{ __('Customer Area') }}
            </a>
            @endauth
            <div class="ml-auto flex items-center gap-x-1 justify-center md:pb-0 pb-4">
                <!-- <button class="m-1.5 button button-secondary-outline !font-normal" id="theme-toggle">
                    <i class="ri-sun-line hidden dark:block"></i>
                    <i class="ri-moon-line dark:hidden"></i>
                </button> -->
                <livewire:cart-count />

                @auth
                @if(Auth::user()->credits > 0 && config('settings::credits'))
                <div class="ml-auto items-center justify-end hidden md:flex">
                    <a href="{{ route('clients.credits') }}" class="text-md mr-2">
                        <i class="ri-wallet-3-line"></i> {{__('Your Balance:')}} <span class="font-semibold">
                            <x-money :amount="Auth::user()->credits" />
                        </span>
                    </a>
                </div>
                <div class="ml-auto flex items-center justify-end md:hidden">
                    <a href="{{ route('clients.credits') }}" class="text-md mr-2">
                        <i class="ri-wallet-3-line"></i> <span class="font-semibold">
                            <x-money :amount="Auth::user()->credits" />
                        </span>
                    </a>
                </div>
                @endif
                <button type="button" aria-expanded="true" aria-haspopup="true" data-dropdown-placement="bottom-end"
                    data-dropdown-toggle="account" class="relative md:flex-none flex-1">
                    <div class="inline-flex items-center justify-center">
                        <img class="w-8 h-8 rounded-md"
                            src="https://www.gravatar.com/avatar/{{md5(Auth::user()->email)}}?s=200&d=mp"
                            alt="Avatar" />
                        <p class="p-2 font-bold">
                            {{ Auth::user()->first_name }}
                        </p>
                    </div>
                    <div class="absolute left-0 hidden w-60 mt-2 origin-top-right bg-secondary-200 border border-secondary-300 rounded-md text-secondary-700 font-normal text-start z-10"
                        role="menu" aria-orientation="vertical" aria-labelledby="product" tabindex="-1" id="account">
                        <div class="px-2 py-2">

                            <a href="{{ route('clients.profile') }}"
                                class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded transition-all ease-in-out">
                                <i class="ri-account-circle-line"></i> {{__('Profile')}}
                            </a>

                            {{-- <a href="#"
                                class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded"><i
                                    class="ri-instance-line"></i> {{ __('Services') }}</a> --}}

                            @if (Auth::user()->has('ADMINISTRATOR'))
                            <a href="{{ route('admin.index') }}"
                                class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded transition-all ease-in-out">
                                <i class="ri-key-2-line"></i> {{ __('Admin area') }}
                            </a>
                            <a href="{{ route('clients.api.index') }}"
                                class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded transition-all ease-in-out">
                                <i class="ri-code-s-slash-line"></i> {{ __('API') }}
                            </a>
                            @endif

                            <hr class="mx-2 my-1 border-secondary-400" />

                            <a type="button" href="{{ route('logout') }}"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded transition-all ease-in-out">
                                <i class="ri-logout-box-line"></i> {{ __('Log Out') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </button>
                @else
                <a href="{{ route('login') }}" class="button button-primary rounded-full font-bold md:flex-none flex-1 mr-4">
                    {{ __('Log In') }}
                </a>
                @endauth
            </div>
        </div>
    </nav>
</header>