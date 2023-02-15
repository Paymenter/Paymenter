<div class="flex-no-wrap hidden 2xl:flex" id="sidebar">
    <div class="w-2/12">
        <aside>
            <div
                class="sidebar fixed top-0 bottom-0 lg:left-0 p-2 w-[300px] overflow-y-auto text-center bg-white dark:bg-gray-900 z-10">
                <div class="text-xl text-gray-700 dark:text-darkmodetext hover:text-white">
                    <a href="{{ route('index') }}"
                        class="p-2.5 items-center mt-1 flex mx-auto duration-300 cursor-pointer hover:bg-blue-900 rounded-md">
                        <x-application-logo />
                        <h1 class="font-bold text-[15px] ml-3">{{ config('settings::app_name') }}</h1>
                    </a>
                    <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600">
                </div>
                <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-gray-700 dark:text-darkmodetext hover:text-white"
                    onclick="dropdownprof()">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex flex-row">
                            <img class="h-8 rounded-md mr-2"
                                src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp"
                                alt="{{ App\Models\User::first()->name }}" />
                            <div class="flex flex-row">
                                <h1 class="p-1 font-bold">{{ App\Models\User::first()->name }}
                                </h1><!-- create a space between the name and the credit -->
                            </div>
                        </div>

                        <svg id="arrowprof" sidebar-toggle-item class="w-6 h-6 transition-all duration-300"
                            fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
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
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="p-2.5 mt-2 flex items-center rounded-md px-2 duration-300 cursor-pointer hover:bg-blue-600 text-red-500 hover:text-white font-bold text-sm"
                        role="menuitem">
                        <i class="ri-logout-box-line ml-2 mr-4 w-4"></i>Sign Out
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
                <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600">
                <x-sidebar-navigation-item route="admin.settings" icon="ri-settings-2-line">
                    Paymenter Settings
                </x-sidebar-navigation-item>
                <div class="p-2.5 mt-3 flex items-center rounded-md h-10 px-4 duration-300 cursor-pointer hover:bg-blue-600 text-gray-700 dark:text-darkmodetext hover:text-white"
                    onclick="dropdowntickets()">
                    <i class="ri-coupon-line"></i>
                    <div class="flex items-center justify-between w-full">
                        <span class="text-[15px] ml-4 font-bold">Tickets</span>
                        <svg id="arrowtickets" sidebar-toggle-item class="w-6 h-6 transition-all duration-300"
                            fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="w-4/5 mx-auto" id="submenutickets">
                    <x-sidebar-navigation-item route="admin.tickets.create" icon="ri-add-circle-line"
                        dropdown="true">
                        Create Ticket
                    </x-sidebar-navigation-item>
                    <x-sidebar-navigation-item route="admin.tickets" icon="ri-coupon-line" dropdown="true">
                        View Ticket
                    </x-sidebar-navigation-item>
                </div>
                <x-sidebar-navigation-item route="admin.products" icon="ri-shopping-basket-2-fill">
                    Products
                </x-sidebar-navigation-item>
                <x-sidebar-navigation-item route="admin.categories" icon="ri-folders-line">
                    Categories
                </x-sidebar-navigation-item>
                <x-sidebar-navigation-item route="admin.orders" icon="ri-file-text-line">
                    Orders
                </x-sidebar-navigation-item>
                <x-sidebar-navigation-item route="admin.clients" icon="ri-user-3-line">
                    Clients
                </x-sidebar-navigation-item>
                <script type="text/javascript">
                    function dropdownprof() {
                        document.querySelector("#submenuprof").classList.toggle("hidden");
                        document.querySelector("#arrowprof").classList.toggle("rotate-180");
                    }
                    dropdownprof();

                    function dropdowntickets() {
                        document.querySelector("#submenutickets").classList.toggle("hidden");
                        document.querySelector("#arrowtickets").classList.toggle("rotate-180");
                    }
                    dropdowntickets();
                </script>
            </div>
        </aside>
    </div>
</div>

<div class="2xl:hidden bottom-0 fixed w-[300px] z-20">
    <div class="flex flex-col flex-1 w-full">
        <header class="z-10 py-4 bg-white dark:bg-gray-900">
            <div class="container flex items-center justify-between h-full px-6 mx-auto text-purple-600 dark:text-purple-300">
                <button class="p-1 mr-5 -ml-1 rounded-md focus:outline-none focus:shadow-outline-purple" aria-label="Menu" onclick="document.querySelector('#sidebar').classList.toggle('hidden');">
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 6a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 6a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </header>
    </div>
</div>
<x-success class="mb-4" />
