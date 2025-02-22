<nav class="w-full md:px-4 bg-background-secondary border-b border-neutral md:h-14 flex md:flex-row flex-col justify-between fixed top-0 z-20">
    <div x-data="{ 
        slideOverOpen: false 
    }"
        x-init="$watch('slideOverOpen', value => { document.documentElement.style.overflow = value ? 'hidden' : '' })"
        class="relative z-50 w-full h-auto">

        <div class="flex flex-row items-center justify-between h-14 px-4">

            <div class="flex flex-row">
                <button @click="slideOverOpen=true" class="flex md:hidden w-10 h-10 items-center justify-center rounded-lg hover:bg-neutral transition">
                    <x-ri-menu-fill class="size-5" />
                </button>
                <a href="{{ route('home') }}" class="flex flex-row items-center" wire:navigate>
                    <x-logo class="h-10" />
                    <span class="text-xl text-base ml-2 font-bold">{{ config('app.name') }}</span>
                </a>
                <div class="md:flex hidden flex-row ml-7">
                    @foreach (\App\Classes\Navigation::getLinks() as $nav)
                    @if (isset($nav['children']) && count($nav['children']) > 0)
                    <div class="relative">
                        <x-dropdown>
                            <x-slot:trigger>
                                <div class="flex flex-col">
                                    <span class="flex flex-row items-center p-3 text-sm font-semibold whitespace-nowrap text-base hover:text-base/80">
                                        {{ $nav['name'] }}
                                    </span>
                                </div>
                            </x-slot:trigger>
                            <x-slot:content>
                                @foreach ($nav['children'] as $child)
                                <x-navigation.link
                                    :href="route($child['route'], $child['params'] ?? null)"
                                    :spa="isset($child['spa']) ? $nav['spa'] : true">
                                    {{ $child['name'] }}
                                </x-navigation.link>
                                @endforeach
                            </x-slot:content>
                        </x-dropdown>
                    </div>
                    @else
                    <x-navigation.link
                        :href="route($nav['route'], $nav['params'] ?? null)"
                        :spa="isset($nav['spa']) ? $nav['spa'] : true"
                        class="flex items-center p-3">
                        {{ $nav['name'] }}
                    </x-navigation.link>
                    @endif
                    {{-- @if($nav['separator'])
                    <div class="h-px w-full bg-neutral"></div>
                    @endif --}}
                    @endforeach

                </div>
            </div>

            <div class="flex flex-row items-center">
                <div class="items-center hidden md:flex">
                    <x-dropdown>
                        <x-slot:trigger>
                            <div class="flex flex-col">
                                <span class="text-sm text-base font-semibold text-nowrap">{{ strtoupper(app()->getLocale()) }} <span class="text-base/50 font-semibold">|</span> {{ session('currency', 'USD') }}</span>
                            </div>
                        </x-slot:trigger>
                        <x-slot:content>
                            <strong class="block p-2 text-xs font-semibold uppercase text-base/50"> Language </strong>
                            <livewire:components.language-switch />
                            <livewire:components.currency-switch />
                        </x-slot:content>
                    </x-dropdown>
                
                    <x-theme-toggle />
                </div>

                <livewire:components.cart />

                @if(auth()->check())
                <x-dropdown>
                    <x-slot:trigger>
                        <img src="{{ auth()->user()->avatar }}" class="size-8 rounded-full border border-neutral bg-background" alt="avatar" />
                    </x-slot:trigger>
                    <x-slot:content>
                        <div class="flex flex-col p-2">
                            <span class="text-sm text-base text-nowrap">{{ auth()->user()->name }}</span>
                            <span class="text-sm text-base text-nowrap">{{ auth()->user()->email }}</span>
                        </div>
                        @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                        <x-navigation.link :href="route($nav['route'], $nav['params'] ?? null)" :spa="isset($nav['spa']) ? $nav['spa'] : true">
                            {{ $nav['name'] }}
                        </x-navigation.link>
                        @endforeach
                        <livewire:auth.logout />
                    </x-slot:content>
                </x-dropdown>
                @else
                <div class="flex flex-row">
                    <x-navigation.link :href="route('login')">{{ __('navigation.login') }}</x-navigation.link>
                    <x-navigation.link :href="route('register')">
                        <x-button.primary>
                            {{ __('navigation.register') }}
                        </x-button.primary>
                    </x-navigation.link>
                    </a>
                </div>
                @endif
            </div>
        </div>
        <template x-teleport="body">
            <div
                x-show="slideOverOpen"
                @keydown.window.escape="slideOverOpen=false"
                class="relative z-[99]">
                <div x-show="slideOverOpen" x-transition.opacity.duration.600ms @click="slideOverOpen = false" class="fixed inset-0 bg-primary/20"></div>
                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="fixed inset-y-0 left-0 flex max-w-full pr-44">
                            <div
                                x-show="slideOverOpen"
                                @click.away="slideOverOpen = false"
                                x-transition:enter="transform transition ease-in-out duration-500"
                                x-transition:enter-start="-translate-x-full"
                                x-transition:enter-end="translate-x-0"
                                x-transition:leave="transform transition ease-in-out duration-500"
                                x-transition:leave-start="translate-x-0"
                                x-transition:leave-end="-translate-x-full"
                                class="w-screen max-w-full">
                                <div class="flex flex-col h-full bg-background-secondary border-r border-neutral shadow-lg">
                                    <div class="px-4 sm:px-5">
                                        <div class="flex items-center justify-between pb-1">
                                            <div class="flex flex-row items-center justify-between h-14 px-4">
                                                <a href="{{ route('home') }}" class="flex flex-row items-center" wire:navigate>
                                                    <x-logo class="h-10" />
                                                    <span class="text-xl text-base ml-2 font-bold">{{ config('app.name') }}</span>
                                                </a>
                                            </div>
                                            <div class="flex items-center h-auto ml-3">
                                                <button @click="slideOverOpen=false" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-neutral transition">
                                                    <x-ri-close-fill class="size-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative flex-1 px-4 mt-5 sm:px-5">
                                        <div class="absolute inset-0 px-4 sm:px-5">
                                            <div class="relative h-full overflow-hidden">
                                                <x-navigation.sidebar-links />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</nav>
