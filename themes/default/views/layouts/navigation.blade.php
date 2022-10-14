<nav class="bg-white border-b border-gray-100">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8 ">
        <div class="flex justify-between h-16 text-gray-500 hover:text-gray-700 text-sm font-medium leading-5 flex-wrap">
            <div class="flex">
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block w-auto h-10 text-gray-600 fill-current" />
                    </a>
                </div>
                <div class="hidden space-x-8 md:-my-px md:ml-10 md:flex">
                    <a href="{{ route('home') }}"
                        class="{{ request()->routeIs('home') ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' : 'border-transparent hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }} inline-flex items-center px-1 pt-1 border-b-2 focus:outline-none">
                        {{ __('Dashboard') }}
                    </a>
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
            <div class="hidden md:flex md:items-center md:ml-6" id="settings">
                @auth
                    <div>{{ Auth::user()->name }}</div>
                    <a href="{{ route('logout') }}"
                        class="h-16 ml-4 border-transparent hover:border-gray-300 focus:text-gray-700 focus:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 focus:outline-none"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="h-16 ml-4 border-transparent hover:border-gray-300 focus:text-gray-700 focus:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 focus:outline-none">
                        {{ __('Login') }}
                    </a>
                    <a href="{{ route('register') }}"
                        class="h-16 ml-4 border-transparent hover:border-gray-300 focus:text-gray-700 focus:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 focus:outline-none">
                        {{ __('Register') }}
                    </a>
                @endauth
                </ul>
            </div>
        </div>
    </div>
    <div class="hidden md:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pt-2 pb-3">
            <a href="#" class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium @if(request()->routeIs('index')) bg-gray-400 @endif" aria-current="page">Dashboard</a>
            <a href="#"
                class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium @if(request()->routeIs('products.*')) bg-gray-400 @endif">Products</a>

            <a href="#"
                class="text-black hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium @if(request()->routeIs('tickets.*')) bg-gray-400 @endif">Projects</a>
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
</nav>
