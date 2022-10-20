<nav class="dark:bg-darkmode bg-white">
    <div class="dark:bg-darkmode px-4 max-w-7xl sm:px-6 lg:px-8">
        <div class="flex dark:bg-darkmode h-16 text-gray-500 hover:text-gray-700 text-sm font-medium leading-5 items-center">
            <div class="flex">
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ route('admin') }}">
                        <x-application-logo class="block w-auto h-10 text-gray-600 fill-current" />
                    </a>
                    <p class="p-2 text-xl font-bold">
                        {{ env('APP_NAME') }}
                    </p>
                </div>
            </div>
            <button data-collapse-toggle="mobile-menu" type="button"
                class="inline-flex justify-center items-center ml-3 text-gray-400 rounded-lg md:hidden"
                aria-controls="mobile-menu-2" aria-expanded="false"
                onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
            <div class="dark:bg-darkmode hidden sm:flex sm:items-center sm:w-auto justify-center place-items-center text-center text-lg dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton"
                id="menu">
                <div class="dark:bg-darkmode relative inline-block text-left">
                    <!-- ticket -->
                    <div class="dark:bg-darkmode">
                        <button type="button" 
                            class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton inline-flex w-full justify-center bg-white pl-4 py-2 text-base font-medium text-gray-700 @if (request()->routeIs('tickets*')) bg-gray-200 @endif"
                            id="menu-button" aria-expanded="true" aria-haspopup="true" onclick="openMenu('tickets')">
                            <i class="ri-shopping-cart-2-line pr-1"
                                @if (request()->routeIs('tickets*')) style="color: #5270FD" @endif></i>Tickets
                            <svg class="-mr-1 ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton absolute right-0 w-56 mt-2 origin-top-right rounded-md shadow-lg hidden dark:bg-darkmode"
                            id="tickets">
                            <div class="dark:bg-darkmode py-1 rounded-md bg-white shadow-xs" role="menu" aria-orientation="vertical"
                                aria-labelledby="options-menu">
                                <a href="{{ route('tickets.index') }}"
                                    class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:"
                                    role="menuitem">List</a>
                                <a href="{{ route('tickets.create') }}"
                                    class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                    role="menuitem">Create</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative inline-block text-left">
                    <button type="button"
                        class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton inline-flex w-full justify-center bg-white pl-4 py-2 text-base font-medium text-gray-700 @if (request()->routeIs('admin.orders*')) bg-gray-200 @endif"
                        id="menu-button" aria-expanded="true" aria-haspopup="true" onclick="openMenu('orders')">
                        <i class="ri-shopping-cart-2-line pr-1"
                            @if (request()->routeIs('products*')) style="color: #5270FD" @endif></i> Products
                        <svg class="-mr-1 ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                        id="orders">
                        <div class="py-1 dark:bg-darkmode" role="none">
                            <a href="{{ route('products.index') }}"
                                class="dark:text-darkmodetext dark:hover:bg-darkbutton text-gray-700 block px-4 py-2 text-base hover:bg-gray-100 hover:text-gray-900"
                                role="menuitem" tabindex="-1" id="menu-item-0">All Products</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dark:bg-darkmode hidden sm:flex sm:items-center w-full sm:w-auto absolute right-0" id="menu">
                @auth
                    <div class="relative inline-block text-left">
                        <button type="button"
                            class="inline-flex w-full justify-center items-center bg-white pl-4 py-2 text-base font-medium text-gray-700"
                            aria-label="User menu" aria-haspopup="true" onclick="openMenu('user-menu')">
                            <!-- use gravatar -->
                            <img class="h-8 w-8 rounded-md"
                                src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp"
                                alt="{{ Auth::user()->name }}" />
                            <p class="p-2 font-bold">
                                {{ Auth::user()->name }}
                            </p>
                        </button>
                        <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden"
                            id="user-menu">
                            <div class="py-1 rounded-md bg-white shadow-xs" role="menu" aria-orientation="vertical"
                                aria-labelledby="user-menu">
                                <a href="" class="block px-4 py-2 text-base text-gray-700 hover:bg-gray-100"
                                    role="menuitem">Your Profile</a>
                                <a href="{{ route('admin.settings') }}"
                                    class="block px-4 py-2 text-base text-gray-700 hover:bg-gray-100"
                                    role="menuitem">Settings</a>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();"
                                    class="block px-4 py-2 text-base text-gray-700 hover:bg-gray-100" role="menuitem">Sign
                                    out</a>
                            </div>
                        </div>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                <!-- login and register links -->
                    <a href="{{ route('login') }}" class="dark:text-darkmodetext dark:bg-darkmode text-sm text-gray-700 transition duration-400 mr-4 p-3 rounded hover:bg-button dark:hover:bg-darkbutton hover:transition duration-400">Log in</a>
                @endauth
            </div>

            <script>
                var usermenu = document.getElementById('user-menu');
                var settings = document.getElementById('settings');
                var orders = document.getElementById('orders');
                var clients = document.getElementById('clients');
                var tickets = document.getElementById('tickets');

                function openMenu(id) {
                    if (id == 'user-menu') {
                        usermenu.classList.toggle('hidden');
                        settings.classList.add('hidden');
                        orders.classList.add('hidden');
                        clients.classList.add('hidden');
                        tickets.classList.add('hidden');
                    } else if (id == 'settings') {
                        settings.classList.toggle('hidden');
                        usermenu.classList.add('hidden');
                        orders.classList.add('hidden');
                        clients.classList.add('hidden');
                        tickets.classList.add('hidden');
                    } else if (id == 'orders') {
                        orders.classList.toggle('hidden');
                        usermenu.classList.add('hidden');
                        settings.classList.add('hidden');
                        clients.classList.add('hidden');
                        tickets.classList.add('hidden');
                    } else if (id == 'clients') {
                        clients.classList.toggle('hidden');
                        usermenu.classList.add('hidden');
                        settings.classList.add('hidden');
                        orders.classList.add('hidden');
                        tickets.classList.add('hidden');
                    } else if (id == 'tickets') {
                        tickets.classList.toggle('hidden');
                        usermenu.classList.add('hidden');
                        settings.classList.add('hidden');
                        orders.classList.add('hidden');
                        clients.classList.add('hidden');
                    }
                }

                function openUserMenu() {
                    document.getElementById("user-menu").classList.toggle("hidden");
                }
            </script>
        </div>
        <div class="hidden md:hidden" id="mobile-menu">
            <div class="space-y-1 px-2 pt-2 pb-3 ">
                <a href="#"
                    class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium @if (request()->routeIs('index')) bg-gray-400 @endif"
                    aria-current="page">Dashboard</a>
                <a href="#"
                    class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium @if (request()->routeIs('products.*')) bg-gray-400 @endif">Products</a>

                @auth
                    <a href="{{ route('logout') }}"
                        class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium"
                        onclick="event.preventDefault();
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
