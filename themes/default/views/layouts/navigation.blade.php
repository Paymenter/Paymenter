<nav class="bg-white dark:bg-darkmode">
    <div class="px-4 dark:bg-darkmode max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center h-16 text-sm font-medium leading-5 text-gray-500 dark:bg-darkmode dark:text-darkmodetext dark:hover:text hover:text-gray-700">
            <a href="{{ route('index') }}">
                <x-application-logo class="block w-auto h-10 text-gray-600 fill-current" />
            </a>
            <a href="{{ route('index') }}" class="p-2 text-xl font-bold">
                {{ config('app.name', 'Paymenter') }}
            </a>
            <button data-collapse-toggle="mobile-menu" type="button" class="inline-flex items-center justify-center ml-3 text-gray-400 rounded-lg md:hidden" aria-controls="mobile-menu-2" aria-expanded="false" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <div class="justify-center hidden text-lg text-center dark:bg-darkmode sm:flex sm:items-center sm:w-auto place-items-center dark:text-darkmodetext id="menu">
                <div class="relative inline-block text-left dark:bg-darkmode">
                    <div class="dark:bg-darkmode">
                        <a type="button" href="{{ route('clients.home') }}" class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton inline-flex w-full justify-center bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('tickets*')) @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" onclick="openMenu('tickets')">
                            <i class="pr-1 ri-dashboard-3-line" @if (request()->routeIs('clients.home*')) style="color: #5270FD" @endif></i>Dashboard
                        </a>
                    </div>
                </div>    
                <div class="relative inline-block text-left dark:bg-darkmode">
                    <!-- ticket -->
                    <div class="dark:bg-darkmode">
                        <a type="button" href="{{ route('clients.tickets.index') }}" class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton inline-flex w-full justify-center bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('tickets*')) @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" onclick="openMenu('tickets')">
                            <i class="pr-1 ri-question-answer-line" @if (request()->routeIs('tickets*')) style="color: #5270FD" @endif></i>Tickets
                        </a>
                    </div>
                </div>
                <div class="relative inline-block text-left">
                    <button type="button" class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton inline-flex w-full justify-center bg-white px-2 py-2 text-base font-medium rounded-md text-gray-700 @if (request()->routeIs('products*')) @endif" id="menu-button" aria-expanded="true" aria-haspopup="true" onclick="openMenu('orders')">
                        <i class="pr-1 ri-shopping-bag-2-line" @if (request()->routeIs('products')) style="color: #5270FD" @endif></i> Products
                        <svg class="w-5 h-5 ml-1 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="orders">
                        <div class="py-1 dark:bg-darkmode" role="none">
                            <a href="{{ route('products') }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkbutton hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-0">All Products</a>
                            @foreach (App\Models\Categories::all() as $category)
                            @if ($category->products->count() > 0)
                            <a href="{{ route('products', ['category' => $category->id]) }}" class="block px-4 py-2 text-base text-gray-700 dark:text-darkmodetext dark:hover:bg-darkbutton hover:bg-gray-100 hover:text-gray-900" role="menuitem" tabindex="-1" id="menu-item-0">{{ $category->name }}</a>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @if (Auth::user() == !null)
                <div class="hidden">
                    <div class="absolute hidden w-full rounded-lg dark:bg-darkmode2 sm:flex sm:items-center sm:w-auto right-40 ">
                        <a class="m-2 dark:bg-darkmode2" href="">
                            {{ number_format(Auth::user()->credit, 2) }}
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <div class="absolute right-0 hidden w-full dark:bg-darkmode sm:flex sm:items-center sm:w-auto" id="menu">
                @auth
                <div class="relative inline-block text-left">
                    <button type="button" class="inline-flex items-center justify-center w-full py-2 pl-4 text-base font-medium text-gray-700 bg-white dark:text-darkmodetext dark:bg-darkmode" aria-label="User menu" aria-haspopup="true" onclick="openMenu('user-menu')">
                        <!-- use gravatar -->
                        <img class="w-8 h-8 rounded-md" src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp" alt="{{ Auth::user()->name }}" />
                        <p class="p-2 font-bold">
                            {{ Auth::user()->name }}
                        </p>
                    </button>
                    <div class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-darkmode ring-1 ring-black ring-opacity-5" id="user-menu">
                        <div class="py-1 bg-white rounded-md shadow-xs dark:bg-darkmode" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                            <a href="{{ route('clients.profile') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">Your Profile</a>
                            @if (Auth::user()->is_admin)
                            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">Settings</a>
                            <a href="{{ route('clients.password.change-password') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">Change Password</a>
                            <div>
                                <a href="{{ route('admin') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">Admin Panel</a>
                            </div>
                            @endif
                            @if (!Auth::user()->is_admin)
                            <div>
                                <a href="{{ route('clients.home') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">Dashboard</a>
                            </div>
                            <div>
                                <a href="{{ route('clients.password.change-password') }}" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">Change Password</a>
                            </div>
                            @endif
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();" class="block px-4 py-2 text-base text-gray-700 dark:hover:bg-darkbutton dark:text-darkmodetext hover:bg-gray-100" role="menuitem">Sign
                                out</a>
                        </div>
                    </div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                @else
                <!-- login and register links -->
                <a href="{{ route('login') }}" class="p-3 mr-4 text-sm text-gray-700 transition rounded dark:text-darkmodetext dark:bg-darkmode duration-400 hover:bg-button dark:hover:bg-darkbutton hover:transition">Log
                    in</a>
                @endauth
                @if (count(session()->get('cart', [])) > 0)
                <a href="{{ route('checkout.index') }}" class="flex p-3 mr-4 text-sm text-center text-gray-700 transition rounded dark:text-darkmodetext dark:bg-darkmode duration-400 hover:bg-button dark:hover:bg-darkbutton hover:transition">
                    <i class="p-1 text-xl ri-shopping-basket-2-line">
                    </i>
                    <span class="inline-flex items-center px-2.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ count(session()->get('cart')) }}</span>

                </a>
                @endif
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

            <script>
                function openMenu(id) {
                    var menu = document.getElementById(id);
                    menu.classList.toggle('hidden');
                    var menuBtn = document.getElementById(id + '-btn');
                    menuBtn.classList.toggle('active');
                    var menus = document.getElementsByClassName('dropdown-menu');
                    for (var i = 0; i < menus.length; i++) {
                        if (menus[i] != menu) {
                            menus[i].classList.add('hidden');
                        }
                    }
                    document.addEventListener('click', function(e) {
                        if (e.target != menu && e.target != menu.previousElementSibling) {
                            menu.classList.add('hidden');
                        }
                    });
                }

                function openUserMenu() {
                    document.getElementById("user-menu").classList.toggle("hidden");
                }

            </script>
        </div>
        <div class="hidden md:hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 ">
                <a href="{{ route('index') }}" class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium @if (request()->routeIs('index')) bg-gray-400 @endif" aria-current="page">Dashboard</a>
                <a href="{{ route('products') }}" class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium @if (request()->routeIs('products.*')) bg-gray-400 @endif">Products</a>


                @auth
                @if (Auth::user()->is_admin)
                <a href="{{ route('admin') }}" class="dark:text-darkmodetext dark:bg-darkmode dark:hover:bg-darkbutton text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium @if (request()->routeIs('admin.*')) bg-gray-400 @endif">Admin</a>
                @endif

                <a href="{{ route('logout') }}" class="block px-3 py-2 text-base font-medium text-black rounded-md hover:bg-gray-100 dark:text-white" onclick="event.preventDefault();
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
