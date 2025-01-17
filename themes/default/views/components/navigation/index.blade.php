<nav class="w-full md:px-4 bg-primary-800 md:h-14 flex md:flex-row flex-col justify-between" x-data="{ mobileMenuOpen: false }">
    <div class="flex flex-row items-center justify-between h-14 w-full px-4">
        <a href="{{ route('home') }}" class="flex flex-row items-center" wire:navigate>
            <x-logo class="h-10" />
            <span class="text-xl text-white ml-2">{{ config('app.name') }}</span>
        </a>
        <button class="flex flex-col md:hidden" x-on:click="mobileMenuOpen = !mobileMenuOpen">
            <!-- hamburger -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </div>

    <div class="flex md:flex flex-col md:flex-row justify-between md:w-fit md:items-center px-4 md:px-0 bg-primary-800 w-full z-10 shadow-lg md:shadow-none"
        x-data="{ accountMenuOpen: false }" :class="{ 'hidden': !mobileMenuOpen }">
        <div class="flex flex-col md:flex-row">
            @foreach (\App\Classes\Navigation::getLinks() as $nav)
                @if (isset($nav['condition']) ? $nav['condition'] : true)
                    @if (isset($nav['children']) && count($nav['children']) > 0)
                        <div class="relative" x-data="{ open: false }">
                            <button class="flex flex-row items-center p-3" x-on:click="open = !open">
                                <span class="text-sm @isset($nav['active']) text-secondary @else text-white @endif">{{ $nav['name'] }}</span>
                                <!-- arrow down -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 @isset($nav['active']) text-secondary @else text-white @endif transform "
                                    fill="none" x-bind:class="{ 'rotate-180': open }" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-1 w-48 bg-primary-800 rounded-md shadow-lg z-10 border border-primary-700 "
                                x-show="open" x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                                x-on:click.outside="open = false" x-cloak>
                                @foreach ($nav['children'] as $child)
                                    <x-navigation.link :href="route($child['route'], $child['params'])" :spa="isset($child['spa']) ? $nav['spa'] : true"
                                    >{{ $child['name'] }}</x-navigation.link>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <x-navigation.link :href="route($nav['route'], $nav['params'] ?? null)" :spa="isset($nav['spa']) ? $nav['spa'] : true">{{ $nav['name'] }}</x-navigation.link>
                    @endif
                @endif
            @endforeach
        </div>
        <livewire:components.cart />
        <livewire:components.currency-switch />
        @if (auth()->check())
            <div class="flex flex-row mb-2 md:mb-0">
                <!-- Has notifications? (updates, errors, etc) (TODO) -->
                <div class="relative">
                    <button class="flex flex-row items-center border border-primary-700 rounded-md px-2 py-1"
                        x-on:click="accountMenuOpen = !accountMenuOpen">
                        <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-full" alt="avatar" />
                        <div class="flex flex-col mx-2">
                            <span class="text-sm text-white text-nowrap">{{ auth()->user()->name }}</span>
                        </div>
                        <!-- arrow down -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-primary-800 rounded-md shadow-lg z-10 border border-primary-700 "
                        x-show="accountMenuOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-90" x-on:click.outside="accountMenuOpen = false" x-cloak>
                        @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $link)
                            @if (isset($link['condition']) ? $link['condition'] : true)
                                <x-navigation.link :href="route($link['route'], $link['params'] ?? null)" :spa="isset($link['spa']) ? $link['spa'] : true">{{ $link['name'] }}</x-navigation.link>
                            @endif
                        @endforeach
                        <livewire:auth.logout />
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-row mb-2 md:mb-0">
                <x-navigation.link :href="route('login')">{{ __('navigation.login') }}</x-navigation.link>
                <x-navigation.link :href="route('register')">{{ __('navigation.register') }}</x-navigation.link>
            </div>
        @endif
    </div>
</nav>
