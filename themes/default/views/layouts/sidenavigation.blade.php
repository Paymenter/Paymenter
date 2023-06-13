<div class="flex flex-no-wrap">
    <div class="w-1/5">
        <aside>
            <div class="sidebar fixed top-0 bottom-0 lg:left-0 p-2 w-[300px] overflow-y-auto text-center shadow-lg bg-white dark:bg-gray-900">
                <div class="text-xl text-gray-700 dark:text-darkmodetext hover:text-white">
                    <a href="{{ route('index') }}" class="p-2.5 items-center mt-1 flex mx-auto duration-300 cursor-pointer hover:bg-blue-900 rounded-md">
                        <x-application-logo />
                        <h1 class="font-bold text-[15px] ml-3">{{ config('settings::app_name') }}</h1>
                    </a>
                    <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600"/>
                </div>
                @auth
                    <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-gray-700 dark:text-darkmodetext hover:text-white" onclick="dropdownprof()">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex flex-row group">
                                <img class="h-8 rounded-md mr-2"
                                    src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp"
                                    alt="{{ App\Models\User::first()->name }}'s avatar" />
                                <div class="flex flex-row">
                                    <h1 class="p-1 font-bold text-inherit">{{ App\Models\User::first()->name }}
                                    </h1><!-- create a space between the name and the credit -->
                                </div>
                            </div>

                            <svg id="arrowprof" sidebar-toggle-item class="w-6 h-6 transition-all duration-300" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="w-4/5 mx-auto" id="submenuprof">
                        <x-sidebar-navigation-item route="clients.home" icon="ri-settings-2-line" dropdown="true">
                            Profile Settings
                        </x-sidebar-navigation-item>
                        <a
                            href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                            class="p-2.5 mt-2 h-10 flex items-center rounded-md px-2 duration-300 cursor-pointer hover:bg-blue-600 text-red-500 hover:text-white font-bold text-sm" role="menuitem">
                            <i class="ri-logout-box-line ml-2 mr-4 w-4"></i>Sign Out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                    <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600">
                @endauth
                @if (Auth::user() == null)
                    <x-sidebar-navigation-item route="login" icon="ri-login-box-line">
                        Login
                    </x-sidebar-navigation-item>
                @endif
                @if (Auth::user() != null)
                    <x-sidebar-navigation-item route="clients.home" icon="ri-dashboard-3-line">
                        Dashboard
                    </x-sidebar-navigation-item>
                    
                    <x-sidebar-navigation-item route="clients.api.index" icon="ri-key-2-line" dropdown="true">
                        API Tokens
                    </x-sidebar-navigation-item>

                    @if (Auth::user()->is_admin == '1')
                        <x-sidebar-navigation-item route="admin.index" icon="ri-admin-line">
                            Admin
                        </x-sidebar-navigation-item>
                    @endif
                @endif
                <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600">
                <x-sidebar-navigation-item route="products" icon="ri-store-2-line">
                    Products
                </x-sidebar-navigation-item>
                <div class="p-2.5 mt-3 flex h-10 items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-gray-700 dark:text-darkmodetext hover:text-white"
                    onclick="dropdown()">
                    <i class="ri-coupon-line text-lg"></i>
                    <div class="flex items-center justify-between w-full">
                        <span class="text-[15px] ml-4 font-bold">Tickets</span>
                        <svg id="arrow" sidebar-toggle-item class="w-6 h-6 transition-all duration-300" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="w-4/5 mx-auto" id="submenu">
                    <x-sidebar-navigation-item route="clients.tickets.create" icon="ri-add-circle-line" dropdown="true">
                        Create Ticket
                    </x-sidebar-navigation-item>
                    <x-sidebar-navigation-item route="clients.tickets.index" icon="ri-coupon-line" dropdown="true">
                        View Tickets
                    </x-sidebar-navigation-item>
                </div>
                <script type="text/javascript">
                    function dropdown() {
                        document.querySelector("#submenu").classList.toggle("hidden");
                        document.querySelector("#arrow").classList.toggle("rotate-180");
                    }
                    dropdown();

                    function dropdownprof() {
                        document.querySelector("#submenuprof").classList.toggle("hidden");
                        document.querySelector("#arrowprof").classList.toggle("rotate-180");
                    }
                    dropdownprof();
                </script>
        </aside>
    </div>
