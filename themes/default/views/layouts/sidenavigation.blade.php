<div class="flex flex-no-wrap">
    <div class="w-1/5">
        <aside>
            <div class="sidebar fixed top-0 bottom-0 lg:left-0 p-2 w-[300px] overflow-y-auto text-center bg-white dark:bg-gray-900">
                <div class="text-xl text-gray-500 dark:text-gray-100">
                    <a href="{{ route('index') }}" class="p-2.5 items-center mt-1 flex mx-auto duration-300 cursor-pointer hover:bg-blue-900 rounded-md">
                        <img src="{{ asset('img/logo.png') }}" alt="logo" class="w-10 h-10 rounded-full" />
                        <h1 class="font-bold text-gray-200 text-[15px] ml-3">{{ config('settings::app_name') }}</h1>
                    </a>
                    <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600"></hr>
                </div>
                @auth
                    <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
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

                            <svg id="arrowprof" sidebar-toggle-item class="w-6 h-6 transition-all duration-300" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="w-4/5 mx-auto" id="submenuprof">
                        <x-navigation-item route="home" icon="bi bi-speedometer" dropdown="true">
                            Profile Settings
                        </x-navigation-item>
                        <a
                            href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                            class="p-2.5 mt-2 flex items-center rounded-md px-2 duration-300 cursor-pointer hover:bg-blue-600 text-gray-200 font-bold text-sm" role="menuitem">
                            <i class="bi bi-box-arrow-in-left ml-2 mr-4 text-white"></i>Sign Out
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                    <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600"></hr>
                @endauth
                @if (Auth::user() == null)
                    <x-navigation-item route="login" icon="bi bi-box-arrow-in-left">
                        Login
                    </x-navigation-item>
                @endif
                @if (Auth::user() != null)
                    <x-navigation-item route="home" icon="bi bi-speedometer">
                        Dashboard
                    </x-navigation-item>
                    @if (Auth::user()->is_admin == '1')
                        <x-navigation-item route="admin" icon="bi bi-person-badge">
                            Admin
                        </x-navigation-item>
                    @endif
                @endif
                <hr class="my-2 border-b-1 border-gray-300 dark:border-gray-600"></hr>
                <x-navigation-item route="products" icon="bi bi-shop">
                    Products
                </x-navigation-item>
                <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
                    onclick="dropdown()">
                    <i class="bi bi-ticket-detailed"></i>
                    <div class="flex items-center justify-between w-full">
                        <span class="text-[15px] ml-4 text-gray-200 font-bold">Tickets</span>
                        <svg id="arrow" sidebar-toggle-item class="w-6 h-6 transition-all duration-300" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
                <div class="w-4/5 mx-auto" id="submenu">
                    <x-navigation-item route="tickets.create" icon="bi bi-plus-circle" dropdown="true">
                        Create Ticket
                    </x-navigation-item>
                    <x-navigation-item route="tickets.index" icon="bi bi-ticket-detailed" dropdown="true">
                        View Tickets
                    </x-navigation-item>
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
