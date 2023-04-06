<nav class="bg-white dark:bg-darkmode dark:text-darkmodetext">
    <div class="px-4 dark:bg-darkmode max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center h-16 text-sm font-medium leading-5 text-gray-500 dark:text-darkmodetext hover:text-gray-700">
            <div class="flex items-center mr-4">
                <a href="{{ route('index') }}">
                    <x-application-logo class="block w-auto h-10 text-gray-600 fill-current" />
                </a>
                <a href="{{ route('index') }}" class="p-2 text-xl font-bold">
                    {{ config('app.name', 'Paymenter') }}
                </a>
            </div>
            <button data-collapse-toggle="mobile-menu" type="button" class="inline-flex items-center justify-center mr-3 text-gray-400 rounded-lg lg:hidden absolute right-0" aria-controls="mobile-menu-2" aria-expanded="false" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <div class="justify-center hidden text-lg text-center lg:flex lg:items-center lg:w-auto place-items-center" id="menu">
                <a href="{{ route('admin.index') }}" class="dark:bg-darkmode dark:text-darkmodetext dark:hover:bg-darkmode2 p-2 rounded-md text-gray-500 hover:text-gray-700 inline-flex justify-center @if (request()->routeIs('admin.index')) bg-gray-200 @endif">
                    <i class="ri-dashboard-line mr-1" @if (request()->routeIs('admin.index')) style="color: #5270FD" @endif></i>
                    {{ __('Dashboard') }}
                </a>
                <div class="relative inline-block text-left">
                    <button type="button" class="dark:bg-darkmode dark:text-darkmodetext dark:hover:bg-darkmode2 inline-flex w-full justify-center bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700  @if (request()->routeIs('admin.clients*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="clients">
                        <i class="pr-1 ri-group-line mr-1" @if (request()->routeIs('admin.clients*')) style="color: #5270FD" @endif></i> Clients
                        <svg class="w-5 h-5 ml-1 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5 z-[1]" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="clients">
                        <div class="py-1 dark:bg-darkmode" role="none">
                            <a href="{{ route('admin.clients') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-0">All Clients</a>
                            <a href="{{ route('admin.clients.create') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-1">Create Client</a>
                        </div>
                    </div>
                </div>
                <div class="relative inline-block text-left">
                    <a href="{{ route('admin.orders') }}" class="dark:bg-darkmode dark:text-darkmodetext dark:hover:bg-darkmode2 inline-flex w-full justify-center bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('admin.orders*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true">
                        <i class="pr-1 ri-shopping-cart-2-line mr-1" @if (request()->routeIs('admin.orders*')) style="color: #5270FD" @endif></i> Orders
                    </a>
                </div>
                <div class="relative inline-block text-left">
                    <button type="button" class="dark:bg-darkmode dark:text-darkmodetext dark:hover:bg-darkmode2 inline-flex w-full justify-center bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('admin.products*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="products">
                        <i class="pr-1 ri-shopping-bag-2-line mr-1" @if (request()->routeIs('admin.products*')) style="color: #5270FD" @endif></i> {{ __('Products') }}
                        <svg class="w-5 h-5 ml-1 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5 z-[1]" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="products">
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.products') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-0">{{ __('All Products') }}</a>
                            <a href="{{ route('admin.products.create') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-1">{{ __('Create Product') }}</a>
                            <hr class="w-full my-1">
                            <a href="{{ route('admin.categories') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-2">{{ __('Categories') }}</a>
                        </div>
                    </div>
                </div>
                <div class="relative inline-block text-left z-[1]">
                    <button type="button" class="dark:bg-darkmode dark:text-darkmodetext dark:hover:bg-darkmode2 inline-flex w-full justify-center bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('admin.tickets*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="support">
                        <i class="pr-1 ri-question-answer-line mr-1" @if (request()->routeIs('admin.tickets')) style="color: #5270FD" @endif></i> {{ __('Support') }}
                        @if (isset(App\Models\Ticket::where('status', 'open')->get()[0]))
                        <span class="inline-flex items-center px-2.5 ml-1 mt-0.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ App\Models\Ticket::where('status', 'open')->get()->count() }}
                        </span>
                        @endif
                        <svg class="w-5 h-5 ml-1 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-white rounded-lg shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="support">
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.tickets') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-0">{{ __('All Support') }}
                                @if (isset(App\Models\Ticket::where('status', 'open')->get()[0]))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ App\Models\Ticket::where('status', 'open')->get()->count() }}
                                </span>
                                @endif
                            </a>
                            <a href="{{ route('admin.tickets') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-1">{{ __('Create Ticket') }}</a>
                        </div>
                    </div>
                </div>

                <div class="relative inline-block text-left">
                    <button type="button" class="dark:bg-darkmode dark:text-darkmodetext dark:hover:bg-darkmode2 inline-flex w-full justify-center bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 mr-4 @if (request()->routeIs('admin.settings') || request()->routeIs('admin.extensions*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="other">
                          <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"></path></svg>
                    </button>
                    <div class="absolute right-0 hidden w-max mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5 z-[1]" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="other">
                        <div class="py-1 grid grid-cols-3" role="none">
                            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-0">{{ __('Settings') }}</a>
                            <a href="{{ route('admin.extensions') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-1">{{ __('Extensions') }}</a>
                            <a href="{{ route('admin.coupons') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-0">{{ __('Coupons') }}</a>
                            <a href="{{ route('admin.announcements')}}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkmode2 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-0">{{ __('Announcements') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute right-0 hidden w-full lg:flex lg:items-center lg:w-auto" id="menu">
                <div class="relative inline-block text-left">
                    <button type="button" class="inline-flex items-center justify-center w-full px-2 py-2 text-base font-medium text-gray-700 bg-white dark:bg-darkmode dark:text-darkmodetext" aria-label="User menu" aria-haspopup="true" data-dropdown-toggle="user-menu">
                        <!-- use gravatar -->
                        <img class="w-8 h-8 rounded-md" src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" alt="{{ Auth::user()->name }}" />
                        <p class="p-2 font-bold">
                            {{ Auth::user()->name }}
                        </p>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5 z-[1]" id="user-menu">
                        <div class="py-1 bg-white rounded-md shadow-xs dark:bg-darkmode" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                            <a href="{{ route('clients.profile') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">{{ __('Your Profile') }}</a>
                            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">{{ __('Settings') }}</a>
                            <a href="{{ route('clients.password.change-password') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">{{ __('Change Password') }}</a> <!-- Temporary -->
                            <a href="{{ route('clients.home') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">{{ __('User Dashboard') }}</a>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">{{ __('Sign out') }}</a>
                        </div>
                    </div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                <div class="flex justify-end col-span-1 pl-1">
                    <button id="theme-toggle" type="button" class="mr-4 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <script>
                    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
                    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

                    // Change the icons inside the button based on previous settings
                    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                            '(prefers-color-scheme: dark)').matches)) {
                        themeToggleLightIcon.classList.remove('hidden');
                    } else {
                        themeToggleDarkIcon.classList.remove('hidden');
                    }

                    var themeToggleBtn = document.getElementById('theme-toggle');

                    themeToggleBtn.addEventListener('click', function() {

                        // toggle icons inside button
                        themeToggleDarkIcon.classList.toggle('hidden');
                        themeToggleLightIcon.classList.toggle('hidden');

                        // if set via local storage previously
                        if (localStorage.getItem('theme')) {
                            if (localStorage.getItem('theme') === 'light') {
                                document.documentElement.classList.add('dark');
                                localStorage.setItem('theme', 'dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                                localStorage.setItem('theme', 'light');
                            }

                            // if NOT set via local storage previously
                        } else {
                            if (document.documentElement.classList.contains('dark')) {
                                document.documentElement.classList.remove('dark');
                                localStorage.setItem('theme', 'light');
                            } else {
                                document.documentElement.classList.add('dark');
                                localStorage.setItem('theme', 'dark');
                            }
                        }

                    });

                </script>
            </div>
        </div>
        <div class="hidden lg:hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('admin.index') }}" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.index')) bg-gray-400 @endif" aria-current="page">{{ __('Dashboard') }}</a>
                <a class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.products.*')) bg-gray-400 @endif" data-dropdown-toggle="products-menu">{{ __('Products') }}</a>
                <div class="dropdown-menu hidden ml-2" id="products-menu">
                    <a href="{{ route('admin.products') }}" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.products')) bg-gray-400 @endif">{{ __('All Products') }}</a>
                    <a href="{{ route('admin.products.create') }}" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.products.create')) bg-gray-400 @endif">{{ __('Add Product') }}</a>
                </div>
            <!-- orders -->
                <a class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.orders.*')) bg-gray-400 @endif" data-dropdown-toggle="orders-menu">{{ __('Orders') }}</a>
                <div class="dropdown-menu hidden ml-2" id="orders-menu">
                    <a href="{{ route('admin.orders') }}" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.orders')) bg-gray-400 @endif">{{ __('All Orders') }}</a>
                </div>
                <!-- clients -->
                <a class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.clients.*')) bg-gray-400 @endif" data-dropdown-toggle="clients-menu">{{ __('Clients') }}</a>
                <div class="dropdown-menu hidden ml-2" id="clients-menu">
                    <a href="{{ route('admin.clients') }}" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.clients')) bg-gray-400 @endif">{{ __('All Clients') }}</a>
                    <a href="{{ route('admin.clients.create') }}" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.clients.create')) bg-gray-400 @endif">{{ __('Add Client') }}</a>
                </div>
                <!-- support -->
                <a class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.tickets.*')) bg-gray-400 @endif" data-dropdown-toggle="support-menu">{{ __('Support') }}</a>
                <div class="dropdown-menu hidden ml-2" id="support-menu">
                    <a href="{{ route('admin.tickets') }}" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.tickets')) bg-gray-400 @endif">{{ __('All Tickets') }}</a>
                    <a href="{{ route('admin.tickets.create') }}" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.tickets.create')) bg-gray-400 @endif">{{ __('Add Ticket') }}</a>
                </div>
                <!-- coupons -->
                <a class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.tickets.*')) bg-gray-400 @endif" href="{{ route('admin.coupons') }}">{{ __('Coupons') }}</a>
                <!-- settings -->
                <a class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.settings.*')) bg-gray-400 @endif" data-dropdown-toggle="settings-menu">{{ __('Settings') }}</a>
                <div class="dropdown-menu hidden ml-2" id="settings-menu">
                    <a href="{{ route('admin.settings') }}" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.settings')) bg-gray-400 @endif">{{ __('Settings') }}</a>
                    <a href="{{ route('admin.extensions') }}" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.extensions')) bg-gray-400 @endif">{{ __('Extension Settings') }}</a>
                </div>

                @auth
                <a href="{{ route('logout') }}" class="block px-3 py-2 text-base font-medium text-black rounded-md hover:bg-gray-100 dark:text-darkmodetext hover:cursor-pointer" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
<x-success class="mb-4" />
