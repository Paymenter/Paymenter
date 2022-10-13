<nav class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 text-gray-500 hover:text-gray-700 text-sm font-medium leading-5">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ route('admin') }}">
                        <x-application-logo class="block w-auto h-10 text-gray-600 fill-current" />
                    </a>
                    <p class="p-2 font-bold">
                        {{ env('APP_NAME') }}
                    </p>
                </div>
            </div>

            <!-- Settings -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- dashboard link -->
                <a href="{{ route('admin') }}" class="p-2 text-gray-500 hover:text-gray-700">
                    {{ __('Dashboard') }}
                </a>
                <div class="relative inline-block text-left">

                    <button type="button"
                        class="inline-flex w-full justify-center rounded-md bg-white pl-4 py-2 text-sm font-medium text-gray-700"
                        id="menu-button" aria-expanded="true" aria-haspopup="true" onclick="openMenu('clients')">
                        Clients <i class="ri-group-line pl-1"></i>
                        <!-- Heroicon name: mini/chevron-down -->
                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                        id="clients" id="menu">
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.clients') }}"
                                class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900"
                                role="menuitem" tabindex="-1" id="menu-item-0">All Clients</a>
                            <a href="{{ route('admin.clients.create') }}"
                                class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900"
                                role="menuitem" tabindex="-1" id="menu-item-1">Create Client</a>
                        </div>
                    </div>
                </div>
                <div class="relative inline-block text-left">
                    <!-- orders -->
                    <button type="button"
                        class="inline-flex w-full justify-center rounded-md bg-white pl-4 py-2 text-sm font-medium text-gray-700"
                        id="menu-button" aria-expanded="true" aria-haspopup="true" onclick="openMenu('orders')">
                        Orders <i class="ri-shopping-cart-2-line pl-1"></i>
                        <!-- Heroicon name: mini/chevron-down -->
                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                        id="orders" id="menu">
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.orders') }}"
                                class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900"
                                role="menuitem" tabindex="-1" id="menu-item-0">All Orders</a>
                            <a href="{{ route('admin.orders.create') }}"
                                class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900"
                                role="menuitem" tabindex="-1" id="menu-item-1">Create Order</a>
                        </div>
                    </div>
                </div>
                <!-- support -->
                <div class="relative inline-block text-left">
                    <button type="button"
                        class="inline-flex w-full justify-center rounded-md bg-white pl-4 py-2 text-sm font-medium text-gray-700"
                        id="menu-button" aria-expanded="true" aria-haspopup="true" onclick="openMenu('support')">
                        Support <i class="ri-question-answer-line pl-1"></i>
                        <!-- Heroicon name: mini/chevron-down -->
                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                        id="support" id="menu">
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.tickets') }}"
                                class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900"
                                role="menuitem" tabindex="-1" id="menu-item-0">All Support</a>
                            <a href="{{ route('admin.tickets') }}"
                                class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900"
                                role="menuitem" tabindex="-1" id="menu-item-1">Create Support</a>
                        </div>
                    </div>
                
                    <div class="absolute right-0 z-10 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                        id="myDropDown" role="menu" aria-orientation="vertical" aria-labelledby="menu-button"
                        tabindex="-1">
                        <div class="py-1" role="none">
                            <!-- Active: "bg-gray-100 text-gray-900", Not Active: "text-gray-700" -->
                            <a href="{{ route('admin.settings') }}" class="text-gray-700 block px-4 py-2 text-sm"
                                role="menuitem" tabindex="-1" id="menu-item-0">General Settings</a>
                        </div>
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.products') }}" class="text-gray-700 block px-4 py-2 text-sm"
                                role="menuitem" tabindex="-1" id="menu-item-2">Products</a>
                            <a href="/orders" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem"
                                tabindex="-1" id="menu-item-3">Orders</a>
                            <a href="{{ route('admin.categories') }}" class="text-gray-700 block px-4 py-2 text-sm"
                                role="menuitem" tabindex="-1" id="menu-item-4">Categories</a>
                        </div>
                        <div class="py-1" role="none">
                            <a href="/customers" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem"
                                tabindex="-1" id="menu-item-4">Customers</a>
                        </div>
                        <div class="py-1" role="none">
                            <a href="{{ route('admin.tickets') }}" class="text-gray-700 block px-4 py-2 text-sm"
                                role="menuitem" tabindex="-1" id="menu-item-6">Tickets
                                @if (isset(App\Models\Tickets::where('status', 'open')->get()[0]))
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ App\Models\Tickets::where('status', 'open')->get()->count() }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>

                <div class="ml-3 relative">
                    <div>
                        <button type="button"
                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out items-center"
                            aria-label="User menu" aria-haspopup="true" onclick="openUserMenu()">
                            <!-- use gravatar -->
                            <img class="h-8 w-8 rounded-md"
                                src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mm"
                                alt="{{ Auth::user()->name }}">
                            <p class="p-2">
                                {{ Auth::user()->name }}
                            </p>
                        </button>
                    </div>
                    <div class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md shadow-lg hidden"
                        id="user-menu">
                        <div class="py-1 rounded-md bg-white shadow-xs" role="menu" aria-orientation="vertical"
                            aria-labelledby="user-menu">
                            <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                role="menuitem">Your Profile</a>
                            <a href="{{ route('admin.settings') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                role="menuitem">Settings</a>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign
                                out</a>
                        </div>
                    </div>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
            <script>
                function openMenu() {
                    document.getElementById("myDropDown").classList.toggle("hidden");
                }

                function openUserMenu() {
                    document.getElementById("user-menu").classList.toggle("hidden");
                }
            </script>
        </div>
    </div>
</nav>
