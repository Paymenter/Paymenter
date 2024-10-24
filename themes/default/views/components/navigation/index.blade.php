<nav class="w-full px-4 bg-primary-800 h-14 flex flex-row justify-between">
    <a href="{{ route('home') }}" class="flex flex-row items-center" wire:navigate>
        <x-logo class="h-10" />
        <span class="text-xl text-white ml-2">{{ config('app.name') }}</span>
    </a>

    <div class="flex flex-row justify-between w-fit items-center" x-data="{ accountMenuOpen: false, mobileMenuOpen: false }">
        <div class="flex flex-row">
            @foreach (\App\Classes\Navigation::get() as $nav)
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
                            x-on:click.outside="open = false">
                            @foreach ($nav['children'] as $child)
                                <x-navigation.link :href="route($child['route'], $child['params'])" :spa="isset($child['spa']) && $child['spa']"
                                   >{{ $child['name'] }}</x-navigation.link>
                            @endforeach
                        </div>
                    </div>
                @else
                    <x-navigation.link :href="route($nav['route'], $nav['params'] ?? null)" :spa="isset($nav['spa']) && $nav['spa']">{{ $nav['name'] }}</x-navigation.link>
                @endif
            @endforeach
        </div>
        <livewire:components.cart />
        @if(auth()->check())
             <div class="flex flex-row">
                 <!-- Has notifications? (updates, errors, etc) (TODO) -->
                 <div class="relative">
                     <button class="flex flex-row items-center border border-primary-700 rounded-md px-2 py-1"
                         x-on:click="accountMenuOpen = !accountMenuOpen">
                         <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-full" alt="avatar" />
                         <div class="flex flex-col ml-2">
                             <span class="text-sm text-white sm:hidden">{{ auth()->user()->initials }}</span>
                             <span class="text-sm text-white hidden sm:block">{{ auth()->user()->name }}</span>
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
                         x-transition:leave-end="opacity-0 scale-90" x-on:click.outside="accountMenuOpen = false">
                        @foreach (\App\Classes\Navigation::getAuth() as $nav)
                            <x-navigation.link :href="route($nav['route'], $nav['params'] ?? null)" :spa="isset($nav['spa']) && $nav['spa']">{{ $nav['name'] }}</x-navigation.link>
                        @endforeach
                         <livewire:auth.logout />
                     </div>
                 </div>
             </div>
        @else
            <div class="flex flex-row">
                <x-navigation.link :href="route('login')">Login</x-navigation.link>
                <x-navigation.link :href="route('register')">Register</x-navigation.link>
            </div>
        @endif
    </div>
</nav>
