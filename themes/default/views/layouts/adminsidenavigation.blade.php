<div class="flex flex-no-wrap">
    <div class="w-2/12">
        <aside>
            <div
            class="sidebar fixed top-0 bottom-0 lg:left-0 p-2 w-[300px] overflow-y-auto text-center bg-gray-900"
            >
            <div class="text-xl text-gray-100">
                <a href="{{ route('index') }}">
                    <div class="p-2.5 mt-1 flex items-center duration-300 cursor-pointer hover:bg-blue-900 rounded-md">
                        <img
                            src="{{ asset('img/logo.png') }}"
                            alt="logo"
                            class="w-10 h-10 rounded-full"
                        />
                        <h1 class="font-bold text-gray-200 text-[15px] ml-3">{{ config('app_name')}}</h1>
                    </div>
                </a>
                <div class="border dark:border-blue-800 my-2 bg-gray-600 h-[1px]"></div>
            </div>
            @auth
                <div
                    class="border dark:border-blue-800 p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
                    onclick="dropdownprof()">
                    <div class="flex items-center justify-between w-full">
                        <div class="flex flex-row">
                            <img class="h-8 rounded-md"
                            src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp"
                            alt="{{ App\Models\User::first()->name }}"/>
                            <div class="flex flex-row">
                                <h1 class="p-1 font-bold">{{ App\Models\User::first()->name }}
                                </h1><!-- create a space between the name and the credit -->
                            </div>
                        </div>

                        <svg id="arrowprof" sidebar-toggle-item class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>
                <div class="w-4/5 mx-auto mt-2 text-sm font-bold text-gray-200" id="submenuprof">
                    <h1 class="p-2 mt-1 border rounded-md cursor-pointer dark:border-blue-800 hover:bg-blue-600">
                    Profile Settings
                    </h1>
                    <h1 class="p-2 mt-1 border rounded-md cursor-pointer dark:border-blue-800 hover:bg-blue-600">
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"
                            class="text-[15px] text-gray-200 font-bold"
                        role="menuitem">Sign Out</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </h1>
                </div>
            @endauth
            @if (Auth::user() == null)
                <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white">
                    <div class="flex items-center justify-between w-full">
                        <a href="{{ route('login') }}" class="text-[15px] ml-4 text-gray-200 font-bold">Login</a>
                    </div>
                </div>   
            @endif
            <div class="border dark:border-blue-800 my-2 bg-gray-600 h-[1px]"></div> 
            <div
                class="border dark:border-blue-800 p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
            >
                <i class="bi bi-bookmark-fill"></i>
                <a href="{{ route('products') }}" class="text-[15px] ml-4 text-gray-200 font-bold">Products</a>
            </div>
            <div
                class="border dark:border-blue-800 p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
                onclick="dropdown()">
                <i class="bi bi-chat-left-text-fill"></i>
                <div class="flex items-center justify-between w-full">
                    <span class="text-[15px] ml-4 text-gray-200 font-bold">Tickets</span>
                    <svg id="arrow" sidebar-toggle-item class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
            <div class="w-4/5 mx-auto mt-2 text-sm font-bold text-gray-200 border dark:border-blue-800" id="submenu">
                <h1 class="p-2 mt-1 rounded-md cursor-pointer hover:bg-blue-600">
                    <a href="{{ route('tickets.create') }}">Create</a>
                </h1>
                <h1 class="p-2 mt-1 rounded-md cursor-pointer hover:bg-blue-600">
                    <a href="{{ route('tickets.index') }}">View</a>
                </h1>
            </div>
            <div class="border dark:border-blue-800 my-2 bg-gray-600 h-[1px]"></div>
            <div class="text-[15px] text-gray-200 font-bold mb-4">Admin Area</div>
            <div
                class="border dark:border-blue-800 p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
            >
                <a href="{{ route('admin.settings') }}" class="text-[15px] ml-4 text-gray-200 font-bold">Application Settings</a>
            </div>
            <div
                class="border dark:border-blue-800 p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
                onclick="dropdowntickets()">
                <i class="bi bi-chat-left-text-fill"></i>
                <div class="flex items-center justify-between w-full">
                    <span class="text-[15px] ml-4 text-gray-200 font-bold">Tickets</span>
                    <svg id="arrowtickets" sidebar-toggle-item class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
            <div class="w-4/5 mx-auto mt-2 text-sm font-bold text-gray-200" id="submenutickets">
                <h1 class="p-2 mt-1 border rounded-md cursor-pointer dark:border-blue-800 hover:bg-blue-600">
                    <a href="{{ route('admin.tickets.create') }}">Create</a>
                </h1>
                <h1 class="p-2 mt-1 border rounded-md cursor-pointer dark:border-blue-800 hover:bg-blue-600">
                    <a href="{{ route('admin.tickets') }}">View</a>
                </h1>
            </div>
            <div
                class="border dark:border-blue-800 p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
            >
                <a href="{{ route('admin.products') }}" class="text-[15px] ml-4 text-gray-200 font-bold">Products</a>
            </div>
            <div
                class="border dark:border-blue-800 p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
            >
                <a href="{{ route('admin.categories') }}" class="text-[15px] ml-4 text-gray-200 font-bold">Categories</a>
            </div>
            <div
                class="border dark:border-blue-800 p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
            >
                <a href="{{ route('admin.orders') }}" class="text-[15px] ml-4 text-gray-200 font-bold">Orders</a>
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

            function dropdowntickets() {
                document.querySelector("#submenutickets").classList.toggle("hidden");
                document.querySelector("#arrowtickets").classList.toggle("rotate-180");
            }
            dropdowntickets();
            </script>
        </aside>
    </div>