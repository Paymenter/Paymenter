<nav class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 text-gray-500 hover:text-gray-700 text-sm font-medium leading-5">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block w-auto h-10 text-gray-600 fill-current" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('home') }}"
                       class="{{ request()->routeIs('home') ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' : 'border-transparent hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }} inline-flex items-center px-1 pt-1 border-b-2 focus:outline-none">
                        {{ __('Dashboard') }}
                    </a>
                </div>
            </div>

            <!-- Settings -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
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
                @endauth
            </div>
        </div>
    </div>
</nav>
