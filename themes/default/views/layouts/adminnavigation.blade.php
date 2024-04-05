<nav class="bg-secondary-50 dark:bg-secondary-100 dark:text-darkmodetext">
    <div class="px-4 dark:bg-secondary-100 max-w-7xl sm:px-6 lg:px-8">
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
                <a href="{{ route('admin.index') }}" class="dark:bg-secondary-100 dark:text-darkmodetext dark:hover:bg-secondary-200 p-2 rounded-md text-gray-500 hover:text-gray-700 inline-flex justify-center @if (request()->routeIs('admin.index')) bg-gray-200 @endif">
                    <i class="ri-dashboard-line mr-1" @if (request()->routeIs('admin.index')) style="color: #5270FD" @endif></i>
                    {{ __('Dashboard') }}
                </a>
                <div class="relative inline-block text-left">
                    <button type="button" class="dark:bg-secondary-100 dark:text-darkmodetext dark:hover:bg-secondary-200 inline-flex w-full justify-center bg-secondary-50 px-2 py-2 text-base font-medium rounded-md text-gray-700  @if (request()->routeIs('admin.clients*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="clients">
                        <i class="pr-1 ri-group-line mr-1" @if (request()->routeIs('admin.clients*')) style="color: #5270FD" @endif></i> {{ __('Clients') }}
                        <svg class="w-5 h-5 ml-1 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-secondary-50 rounded-lg shadow-lg dark:bg-secondary-300 ring-1 ring-black ring-opacity-5 z-10" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="clients">
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.clients') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">{{__('All Clients')}}</a>
                            <a href="{{ route('admin.clients.create') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-1">{{__('Create Client')}}</a>
                        </div>
                    </div>
                </div>
                <div class="relative inline-block text-left">
                    <a href="{{ route('admin.orders') }}" class="dark:bg-secondary-100 dark:text-darkmodetext dark:hover:bg-secondary-200 inline-flex w-full justify-center bg-secondary-50 px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('admin.orders*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true">
                        <i class="pr-1 ri-shopping-cart-2-line mr-1" @if (request()->routeIs('admin.orders*')) style="color: #5270FD" @endif></i> {{__('Orders')}}
                    </a>
                </div>
                <div class="relative inline-block text-left">
                    <a href="{{ route('admin.invoices') }}" class="dark:bg-secondary-100 dark:text-darkmodetext dark:hover:bg-secondary-200 inline-flex w-full justify-center bg-secondary-50 px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('admin.invoices*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true">
                        <i class="pr-1 ri-bill-line mr-1" @if (request()->routeIs('admin.invoices*')) style="color: #5270FD" @endif></i> {{__('Invoices')}}
                    </a>
                </div>
                <div class="relative inline-block text-left">
                    <button type="button" class="dark:bg-secondary-100 dark:text-darkmodetext dark:hover:bg-secondary-200 inline-flex w-full justify-center bg-secondary-50 px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('admin.products*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="products">
                        <i class="pr-1 ri-shopping-bag-2-line mr-1" @if (request()->routeIs('admin.products*')) style="color: #5270FD" @endif></i> {{ __('Products') }}
                        <svg class="w-5 h-5 ml-1 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-secondary-50 rounded-lg shadow-lg dark:bg-secondary-300 ring-1 ring-black ring-opacity-5 z-10" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="products">
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.products') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">{{ __('All Products') }}</a>
                            <a href="{{ route('admin.products.create') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-1">{{ __('Create Product') }}</a>
                            <hr class="w-full my-1">
                            <a href="{{ route('admin.categories') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-2">{{ __('Categories') }}</a>
                        </div>
                    </div>
                </div>
                <div class="relative inline-block text-left z-[1]">
                    @php $unread = App\Models\Ticket::where('status', 'open')->count(); @endphp
                    <button type="button" class="dark:bg-secondary-100 dark:text-darkmodetext dark:hover:bg-secondary-200 inline-flex w-full justify-center bg-secondary-50 px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('admin.tickets*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="support">
                        <i class="pr-1 ri-question-answer-line mr-1" @if (request()->routeIs('admin.tickets')) style="color: #5270FD" @endif></i> {{ __('Support') }}
                        @if ($unread > 0)
                            <span class="inline-flex items-center px-2.5 ml-1 mt-0.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $unread }}
                        </span>
                        @endif
                        <svg class="w-5 h-5 ml-1 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-secondary-50 rounded-lg shadow-lg dark:bg-secondary-300 ring-1 ring-black ring-opacity-5 z-10" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="support">
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.tickets') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">{{ __('All Support') }}
                                @if ($unread > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                     {{ $unread }}
                                </span>
                                @endif
                            </a>
                            <a href="{{ route('admin.tickets.create') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-1">{{ __('Create Ticket') }}</a>
                        </div>
                    </div>
                </div>
                <div class="relative inline-block text-left">
                    <button type="button" class="dark:bg-secondary-100 dark:text-darkmodetext dark:hover:bg-secondary-200 inline-flex w-full justify-center bg-secondary-50 px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('admin.email*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="email">
                        <i class="pr-1 ri-mail-line mr-1" @if (request()->routeIs('admin.email*')) style="color: #5270FD" @endif></i> {{ __('Emails') }}
                        <svg class="w-5 h-5 ml-1 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-secondary-50 rounded-lg shadow-lg dark:bg-secondary-300 ring-1 ring-black ring-opacity-5 z-10" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="email">
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.email') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-1">{{ __('Logs') }}</a>
                        </div>
                    </div>
                </div>

                <div class="relative inline-block text-left">
                    <button type="button" class="dark:bg-secondary-100 dark:text-darkmodetext dark:hover:bg-secondary-200 inline-flex w-full justify-center bg-secondary-50 px-2 py-2 text-base font-medium rounded-md text-gray-700 mr-4 @if (request()->routeIs('admin.settings') || request()->routeIs('admin.extensions*')) bg-gray-200 @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" data-dropdown-toggle="other">
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden w-max mt-2 origin-top-right bg-secondary-50 rounded-md shadow-lg dark:bg-secondary-300 ring-1 ring-black ring-opacity-5 z-10" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="other">
                        <div class="py-1 grid grid-cols-3" role="none">
                            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">{{ __('Settings') }}</a>
                            <a href="{{ route('admin.extensions') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-1">{{ __('Extensions') }}</a>
                            <a href="{{ route('admin.coupons') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">{{ __('Coupons') }}</a>
                            <a href="{{ route('admin.announcements')}}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">{{ __('Announcements') }}</a>                            
                            <a href="{{ route('admin.roles') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">{{ __('Roles') }}</a>
                            <a href="{{ route('admin.taxes') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">{{ __('Taxes') }}</a>
                            <a href="{{ route('admin.logs') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1">{{ __('Logs') }}</a>
                            <a href="{{ route('admin.configurable-options')}}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-secondary-200 hover:bg-gray-100 hover:text-gray-900 col-span-3" role="menuitem" tabindex="-1">{{ __('Configurable Options') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute right-0 hidden w-full lg:flex lg:items-center lg:w-auto" id="menu">
                <div class="relative inline-block text-left">
                    <button type="button" class="inline-flex items-center justify-center w-full px-2 py-2 text-base font-medium text-gray-700 bg-secondary-50 dark:bg-secondary-100 dark:text-darkmodetext" aria-label="User menu" aria-haspopup="true" data-dropdown-toggle="user-menu">
                        <!-- use gravatar -->
                        <img class="w-8 h-8 rounded-md" src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" alt="{{ Auth::user()->name }}" />
                        <p class="p-2 font-bold">
                            {{ Auth::user()->name }}
                        </p>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-secondary-50 rounded-md shadow-lg dark:bg-secondary-300 ring-1 ring-black ring-opacity-5 z-10" id="user-menu">
                        <div class="py-1 shadow-xl bg-secondary-50 rounded-md dark:bg-secondary-100" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-mobile">
                            <a href="{{ route('clients.profile') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem"><i class="ri-user-line"></i> {{ __('Your Profile') }}</a>
                            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem"><i class="ri-settings-3-line"></i> {{ __('Settings') }}</a>
                            <a href="{{ route('clients.password.change-password') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem"><i class="ri-key-2-line"></i> {{ __('Change Password') }}</a> <!-- Temporary -->
                            <a href="{{ route('clients.home') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem"><i class="ri-dashboard-line"></i> {{ __('User Dashboard') }}</a>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem"><i class="ri-logout-box-line"></i> {{ __('Sign out') }}</a>
                        </div>
                    </div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
        <div class="hidden lg:hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.index') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.index')) bg-secondary-200 @endif" aria-current="page">{{ __('Dashboard') }}</a>

                <!-- Products -->
                <a class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.products*')) bg-secondary-200 @endif" data-dropdown-toggle="products-menu">{{ __('Products') }}</a>
                <div class="dropdown-menu bg-secondary-200 p-2 rounded-md shadow-sm hidden ml-2" id="products-menu">
                    <a href="{{ route('admin.products') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.products')) bg-secondary-200 @endif">{{ __('All Products') }}</a>
                    <a href="{{ route('admin.products.create') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.products.create')) bg-secondary-200 @endif">{{ __('Add Product') }}</a>
                </div>

                <!-- Orders -->
                <a class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.orders*')) bg-secondary-200 @endif" data-dropdown-toggle="orders-menu">{{ __('Orders') }}</a>
                <div class="dropdown-menu bg-secondary-200 p-2 rounded-md shadow-sm hidden ml-2" id="orders-menu">
                    <a href="{{ route('admin.orders') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.orders*')) bg-secondary-200 @endif">{{ __('All Orders') }}</a>
                </div>

                <!-- Invoices -->
                <a  href="{{ route('admin.invoices') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.invoices*')) bg-secondary-200 @endif">{{ __('Invoices') }}</a>

                <!-- clients -->
                <a class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.clients*')) bg-secondary-200 @endif" data-dropdown-toggle="clients-menu">{{ __('Clients') }}</a>
                <div class="dropdown-menu bg-secondary-200 p-2 rounded-md shadow-sm hidden ml-2" id="clients-menu">
                    <a href="{{ route('admin.clients') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.clients')) bg-secondary-200 @endif">{{ __('All Clients') }}</a>
                    <a href="{{ route('admin.clients.create') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.clients.create')) bg-secondary-200 @endif">{{ __('Add Client') }}</a>
                </div>

                <!-- support -->
                <a class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.tickets*')) bg-secondary-200 @endif" data-dropdown-toggle="support-menu">{{ __('Support') }}</a>
                <div class="dropdown-menu bg-secondary-200 p-2 rounded-md shadow-sm hidden ml-2" id="support-menu">
                    <a href="{{ route('admin.tickets') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.tickets')) bg-secondary-200 @endif">{{ __('All Tickets') }}</a>
                    <a href="{{ route('admin.tickets.create') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.tickets.create')) bg-secondary-200 @endif">{{ __('Add Ticket') }}</a>
                </div>

                <!-- coupons -->
                <a class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.tickets*')) bg-secondary-200 @endif" href="{{ route('admin.coupons') }}">{{ __('Coupons') }}</a>

                <!-- settings -->
                <a class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext hover:cursor-pointer @if (request()->routeIs('admin.settings*')) bg-secondary-200 @endif" data-dropdown-toggle="settings-menu">{{ __('Settings') }}</a>
                <div class="dropdown-menu bg-secondary-200 p-2 rounded-md shadow-sm hidden ml-2 z-10" id="settings-menu">
                    <a href="{{ route('admin.settings') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.settings*')) bg-secondary-200 @endif">{{ __('Settings') }}</a>
                    <a href="{{ route('admin.extensions') }}" class="text-black hover:bg-secondary-300 block px-3 py-2 rounded-md text-base font-medium dark:text-darkmodetext @if (request()->routeIs('admin.extensions*')) bg-secondary-200 @endif">{{ __('Extension Settings') }}</a>
                </div>

                @auth
                    <div class="w-full" id="menu">
                        <div class="relative inline-block text-left">
                            <button type="button" class="inline-flex items-center justify-center w-full px-2 py-2 text-base font-medium text-gray-700 bg-secondary-50 dark:bg-secondary-100 dark:text-darkmodetext" aria-label="User menu" aria-haspopup="true" data-dropdown-toggle="user-menu-mobile">
                                <!-- use gravatar -->
                                <img class="w-8 h-8 rounded-md" src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" alt="{{ Auth::user()->name }}" />
                                <p class="p-2 font-bold">
                                    {{ Auth::user()->name }}
                                </p>
                            </button>
                            <div class="absolute shadow-xl hidden right-0 w-56 mt-1 origin-top-right bg-secondary-50 rounded-md dark:bg-secondary-300 ring-1 ring-black ring-opacity-5 z-[1]" id="user-menu-mobile">
                                <div class="py-1 shadow-xl bg-secondary-50 rounded-md dark:bg-secondary-100" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-mobile">
                                    <a href="{{ route('clients.profile') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem"><i class="ri-user-line"></i> {{ __('Your Profile') }}</a>
                                    <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem"><i class="ri-settings-3-line"></i> {{ __('Settings') }}</a>
                                    <a href="{{ route('clients.password.change-password') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem"><i class="ri-key-2-line"></i> {{ __('Change Password') }}</a> <!-- Temporary -->
                                    <a href="{{ route('clients.home') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem"><i class="ri-dashboard-line"></i> {{ __('User Dashboard') }}</a>
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem"><i class="ri-logout-box-line"></i> {{ __('Sign out') }}</a>
                                </div>
                            </div>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
