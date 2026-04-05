<nav class="w-full px-4 lg:px-8 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 md:h-16 flex md:flex-row flex-col justify-between fixed top-0 z-50 shadow-lg">
    <div
        x-data="{ 
            slideOverOpen: false,
            hasAside: !!document.getElementById('main-aside')
        }"
        x-init="$watch('slideOverOpen', value => { document.documentElement.style.overflow = value ? 'hidden' : '' })"
        class="relative w-full h-auto">
        <div
            class="flex flex-row items-center justify-between h-16"
            :class="hasAside ? 'w-full' : 'container mx-auto'">

            {{-- Logo Section --}}
            <div class="flex flex-row items-center">
                <a href="{{ route('home') }}" class="flex flex-row items-center h-10 gap-2.5 group" wire:navigate>
                    <x-logo class="h-8 transition-all duration-300 group-hover:scale-105 group-hover:rotate-3" />
                    @if(theme('logo_display', 'logo-and-name') != 'logo-only')
                    <span class="text-xl font-bold leading-none flex items-center bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400 bg-clip-text text-transparent group-hover:opacity-80 transition-opacity">
                        {{ config('app.name') }}
                    </span>
                    @endif
                </a>
                
                {{-- Desktop Navigation Links --}}
                <div class="md:flex hidden flex-row ml-8">
                    @foreach (\App\Classes\Navigation::getLinks() as $nav)
                    @if (isset($nav['children']) && count($nav['children']) > 0)
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            @click="open = !open"
                            @click.away="open = false"
                            @keydown.escape.window="open = false"
                            class="flex flex-row items-center px-3 py-2 text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all duration-200">
                            {{ $nav['name'] }}
                            <svg class="w-4 h-4 ml-1 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div 
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="absolute left-0 mt-2 min-w-[200px] bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-800 overflow-hidden z-50"
                            style="display: none;">
                            <div class="py-2">
                                @foreach ($nav['children'] as $child)
                                <a href="{{ $child['url'] }}" 
                                   @if(isset($child['spa']) ? $child['spa'] : true) wire:navigate @endif
                                   class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-150">
                                    {{ $child['name'] }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ $nav['url'] }}"
                       @if(isset($nav['spa']) ? $nav['spa'] : true) wire:navigate @endif
                       class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-all duration-200">
                        {{ $nav['name'] }}
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Right Side Actions --}}
            <div class="flex flex-row items-center gap-2">
                <livewire:components.cart />

                {{-- Language & Currency Dropdown - FIXED HOVER ISSUE --}}
                <div x-data="{ open: false }" class="items-center hidden md:flex relative">
                    <button 
                        @mouseenter="open = true"
                        @mouseleave="open = false"
                        @click.away="open = false"
                        @keydown.escape.window="open = false"
                        class="text-sm font-medium text-gray-700 dark:text-gray-200 text-nowrap px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 flex items-center gap-1">
                        <x-ri-global-line class="size-4" />
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
                        <span class="text-gray-400 dark:text-gray-600 mx-1">|</span>
                        <span>{{ \App\Models\Currency::find(session('currency', config('settings.default_currency')))?->code ?? session('currency', config('settings.default_currency')) }}</span>
                        <svg class="w-4 h-4 ml-1 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div 
                        x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        @mouseenter="open = true"
                        @mouseleave="open = false"
                        class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-800 overflow-hidden z-50"
                        style="display: none;">
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <x-ri-translate-2 class="size-4 text-primary-500" />
                                <strong class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Language</strong>
                            </div>
                            <livewire:components.language-switch />
                            <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>
                            <div class="flex items-center gap-2 mb-3">
                                <x-ri-money-dollar-circle-line class="size-4 text-primary-500" />
                                <strong class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">Currency</strong>
                            </div>
                            <livewire:components.currency-switch />
                        </div>
                    </div>
                </div>
                
                <x-theme-toggle />

                {{-- Authenticated User Section --}}
                @if(auth()->check())
                <livewire:components.notifications />
                
                {{-- Profile Dropdown --}}
                <div x-data="{ profileOpen: false }" class="hidden lg:block relative">
                    <button 
                        @click="profileOpen = !profileOpen"
                        @click.away="profileOpen = false"
                        @keydown.escape.window="profileOpen = false"
                        class="focus:outline-none group relative flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                        aria-label="Profile menu">
                        <img src="{{ auth()->user()->avatar }}" class="size-8 rounded-full border-2 border-transparent group-hover:border-primary-500 dark:group-hover:border-primary-400 transition-all duration-200 cursor-pointer" alt="avatar" />
                        <span class="hidden xl:inline text-sm font-medium text-gray-700 dark:text-gray-200">{{ auth()->user()->name }}</span>
                        <svg class="hidden xl:block w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': profileOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div
                        x-show="profileOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-800 overflow-hidden z-50"
                        style="display: none;">
                        
                        <div class="flex flex-col p-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800/50 dark:to-gray-900 border-b border-gray-200 dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <img src="{{ auth()->user()->avatar }}" class="size-12 rounded-full border-2 border-primary-500" alt="avatar" />
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="py-2">
                            @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                            <a 
                                href="{{ $nav['url'] }}" 
                                @if(isset($nav['spa']) && $nav['spa']) wire:navigate @endif
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-150">
                                @if(isset($nav['icon']))
                                    <x-dynamic-component :component="$nav['icon']" class="size-4" />
                                @else
                                    <x-ri-user-line class="size-4" />
                                @endif
                                {{ $nav['name'] }}
                            </a>
                            @endforeach
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-gray-800">
                            <livewire:auth.logout />
                        </div>
                    </div>
                </div>
                @else
                {{-- Guest Section --}}
                <div class="hidden lg:flex flex-row gap-2">
                    <a href="{{ route('login') }}" wire:navigate>
                        <x-button.secondary class="px-5 py-2 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md">
                            <x-ri-login-circle-line class="size-4 mr-1" />
                            {{ __('navigation.login') }}
                        </x-button.secondary>
                    </a>
                    @if(!config('settings.registration_disabled', false))
                    <a href="{{ route('register') }}" wire:navigate>
                        <x-button.primary class="px-5 py-2 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-lg hover:scale-105">
                            <x-ri-user-add-line class="size-4 mr-1" />
                            {{ __('navigation.register') }}
                        </x-button.primary>
                    </a>
                    @endif
                </div>
                @endif

                {{-- Mobile Menu Button --}}
                <button
                    @click="slideOverOpen = !slideOverOpen"
                    class="relative w-10 h-10 flex lg:hidden items-center justify-center rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                    aria-label="Toggle Menu">

                    <span
                        x-show="!slideOverOpen"
                        x-transition:enter="transition duration-300"
                        x-transition:enter-start="opacity-0 -rotate-90 scale-75"
                        x-transition:enter-end="opacity-100 rotate-0 scale-100"
                        x-transition:leave="transition duration-150"
                        x-transition:leave-start="opacity-100 rotate-0 scale-100"
                        x-transition:leave-end="opacity-0 rotate-90 scale-75"
                        class="absolute inset-0 flex items-center justify-center text-gray-700 dark:text-gray-200"
                        aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </span>

                    <span
                        x-show="slideOverOpen"
                        x-transition:enter="transition duration-300"
                        x-transition:enter-start="opacity-0 rotate-90 scale-75"
                        x-transition:enter-end="opacity-100 rotate-0 scale-100"
                        x-transition:leave="transition duration-150"
                        x-transition:leave-start="opacity-100 rotate-0 scale-100"
                        x-transition:leave-end="opacity-0 -rotate-90 scale-75"
                        class="absolute inset-0 flex items-center justify-center text-gray-700 dark:text-gray-200"
                        aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>

        {{-- Mobile Slide-over Panel --}}
        <template x-teleport="body">
            <div
                x-show="slideOverOpen"
                @keydown.window.escape="slideOverOpen=false"
                x-cloak
                class="fixed inset-0 top-16 w-full z-[100]"
                style="height:calc(100dvh - 4rem);"
                aria-modal="true"
                tabindex="-1">
                
                {{-- Backdrop --}}
                <div
                    x-show="slideOverOpen"
                    x-transition:enter="transition-opacity duration-300 ease-out"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                    @click="slideOverOpen = false">
                </div>

                {{-- Panel Content --}}
                <div
                    x-show="slideOverOpen"
                    x-transition:enter="transition-transform duration-300 ease-out"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition-transform duration-200 ease-in"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="absolute right-0 top-0 bottom-0 w-full max-w-sm bg-white dark:bg-gray-900 shadow-2xl overflow-y-auto flex flex-col">

                    <div class="flex flex-col h-full">
                        {{-- Mobile Header --}}
                        <div class="sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 p-4 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <x-logo class="h-6" />
                                <span class="font-bold text-sm">{{ config('app.name') }}</span>
                            </div>
                            <button @click="slideOverOpen = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto p-4">
                            <x-navigation.sidebar-links />
                        </div>
                        
                        <div class="sticky bottom-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 p-4">
                            @if(auth()->check())
                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl mb-3">
                                    <img src="{{ auth()->user()->avatar }}" class="size-10 rounded-full border border-gray-200 dark:border-gray-700" alt="avatar" />
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                                <livewire:auth.logout />
                            @else
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('login') }}" wire:navigate class="w-full">
                                        <x-button.secondary class="w-full justify-center">
                                            {{ __('navigation.login') }}
                                        </x-button.secondary>
                                    </a>
                                    @if(!config('settings.registration_disabled', false))
                                    <a href="{{ route('register') }}" wire:navigate>
                                        <x-button.primary class="w-full justify-center">
                                            {{ __('navigation.register') }}
                                        </x-button.primary>
                                    </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</nav>