@if (config('settings::sidebar') == 0)
    <div class="bg-secondary-50 dark:bg-secondary-100 dark:border-0 dark:border-t-2 border-b-2 md:block border-secondary-200 hidden" id="clientsNavBar">
        <div class="max-w-[1650px] mx-auto block md:flex items-center gap-x-10 px-5">
            <a href="{{ route('clients.home') }}" class="md:px-2 py-3 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-layout-2-line @if (request()->routeIs('clients.home')) text-primary-400 @endif"></i> {{ __('Dashboard') }}
            </a>
            <!-- Will be added in the next update
            <a href="#" class="md:px-2 py-3 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-instance-line"></i> {{ __('Services') }}
            </a>
            -->
            <a href="{{ route('clients.invoice.index') }}" class="md:px-2 py-3 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-file-paper-line @if (request()->routeIs('clients.invoice*')) text-primary-400 @endif"></i> {{ __('Invoices') }}
            </a>
            <a href="{{ route('clients.tickets.index') }}" class="md:px-2 py-3 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-customer-service-2-line @if (request()->routeIs('clients.tickets*')) text-primary-400 @endif"></i> {{ __('Tickets') }}
            </a>
            <a href="{{ route('clients.profile') }}" class="md:px-2 py-3 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-user-6-line @if (request()->routeIs('clients.profile')) text-primary-400 @endif"></i> {{ __('Profile Settings') }}
            </a>
        </div>
    </div>
@else
    <div class="max-w-[1650px] flex-wrap items-center w-full justify-between flex md:hidden px-4 py-4" id="mobile-menu">
        <a href="{{ route('index') }}" class="flex items-center text-secondary-900 font-semibold text-lg gap-x-2">
            <x-application-logo class="w-10" />
            {{ config('app.name', 'Paymenter') }}
        </a>
        <!-- Mobile menu button -->
        <div class="flex md:hidden">
            <button type="button" class="button button-secondary-outline" onclick="openMobileMenu()">
                <i class="ri-menu-line"></i>
            </button>
        </div>
        <script>
            function openMobileMenu() {
                document.getElementById("mobile-menu").classList.toggle("opacity-0");
                document.getElementById("clientsNavBar").classList.toggle("hidden");
            }
        </script>
    </div>

    <div class="shrink-0 md:w-64 w-72 hidden sm:block md:sticky fixed top-0" id="clientsNavBar">
        <div class="bg-secondary-50 dark:bg-secondary-100 dark:border-0 border-r-2 border-secondary-200 h-screen sticky top-0 px-4 py-2 flex flex-col">
            <div class=" flex flex-wrap items-center w-full justify-between">
                <a href="{{ route('index') }}" class="flex items-center text-secondary-900 font-semibold text-lg py-2 gap-x-2">
                    <x-application-logo class="w-10" />
                    {{ config('app.name', 'Paymenter') }}
                </a>
            </div>
            <span class="text-sm text-secondary-600">General</span>
            <a href="{{ route('index') }}" class="py-1 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-home-line @if (request()->routeIs('index')) text-primary-400 @endif"></i> {{ __('Home') }}
            </a>
            <a href="{{ route('announcements.index') }}" class="py-1 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-megaphone-line @if (request()->routeIs('announcements.index')) text-primary-400 @endif"></i> {{ __('Announcements') }}
            </a>
            <a href="{{ route('clients.tickets.index') }}" class="py-1 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-customer-service-line @if (request()->routeIs('clients.tickets.index')) text-primary-400 @endif"></i> {{ __('Help Center') }}
            </a>
            <span class="text-sm text-secondary-600 mt-3">Dashboard</span>
            <a href="{{ route('clients.home') }}" class="py-1 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-layout-2-line @if (request()->routeIs('clients.home')) text-primary-400 @endif"></i> {{ __('Dashboard') }}
            </a>
            <!-- Will be added in the next update
            <a href="#" class="py-1 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-instance-line"></i> {{ __('Services') }}
            </a>
             -->
            <a href="{{ route('clients.invoice.index') }}" class="py-1 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-file-paper-line @if (request()->routeIs('clients.invoice*')) text-primary-400 @endif""></i> {{ __('Invoices') }}
            </a>
            <a href="{{ route('clients.tickets.index') }}" class="py-1 flex items-center gap-x-2 hover:text-secondary-800 duration-300">
                <i class="ri-customer-service-2-line @if (request()->routeIs('clients.tickets*')) text-primary-400 @endif"></i> {{ __('Tickets') }}
            </a>

            <div class="mt-auto pb-2">
                <div class="flex items-center gap-x-2">
                    <a href="{{ route('clients.profile') }}" class="flex items-center gap-x-2 overflow-hidden">
                        <img class="w-7 h-7 rounded-md"
                            src="https://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?s=200&d=mp"
                            alt="{{ Auth::user()->name }}" />
                        <p class="leading-4">{{ Auth::user()->name }}</p>
                    </a>
                    <button type="button" aria-expanded="true" aria-haspopup="true" data-dropdown-placement="top"
                    data-dropdown-toggle="account" class="ml-auto button button-secondary-outline relatve">
                        <i class="ri-more-2-line"></i>
                        <div class="absolute left-0 hidden w-60 mt-2 origin-top-right bg-secondary-200 border border-secondary-300 rounded-md text-secondary-700 font-normal text-start z-10"
                        role="menu" aria-orientation="vertical" aria-labelledby="product" tabindex="-1" id="account">
                            <div class="px-2 py-2">
                                {{-- <a href="#" class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded"><i class="ri-instance-line"></i> {{ __('Services') }}</a> --}}
                                @if (Auth::user()->is_admin)
                                    <a href="{{ route('admin.index') }}"
                                        class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded"><i
                                            class="ri-key-2-line"></i> {{ __('Admin area') }}</a>
                                    <a href="{{ route('clients.api.index') }}"
                                        class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded"><i
                                            class="ri-code-s-slash-line"></i>
                                        {{ __('API') }}</a>
                                @endif
                                <hr class="mx-2 my-1 border-secondary-400" />
                                <a type="button" href="{{ route('logout') }}"
                                    class="px-2 py-2 hover:bg-secondary-300 flex items-center gap-x-2 rounded"
                                    onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                                        class="ri-logout-box-line"></i> {{ __('Log Out') }}</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
        <div class="fixed md:hidden top-0 w-screen h-full bg-black/50 backdrop-blur z-[-1]" onclick="openMobileMenu()">
        </div>
    </div>
@endif