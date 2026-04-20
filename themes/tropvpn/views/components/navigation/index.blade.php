{{-- TropVPN themed top navigation bar --}}
<nav class="w-full fixed top-0 z-20">
    <div class="px-4 lg:px-6 py-3">
        <div
            x-data="{
                slideOverOpen: false,
                hasAside: !!document.getElementById('main-aside')
            }"
            x-init="$watch('slideOverOpen', value => { document.documentElement.style.overflow = value ? 'hidden' : '' })"
        >
            {{-- Main nav pill --}}
            <div class="flex items-center justify-between px-4 sm:px-5 py-3 rounded-2xl
                        bg-background-secondary/80 backdrop-blur-xl border border-neutral/50
                        shadow-lg shadow-black/10"
                 :class="hasAside ? 'w-full' : 'container mx-auto'">

                {{-- Logo --}}
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-primary/40 rounded-xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative h-9 w-9 rounded-xl overflow-hidden bg-background border border-neutral/50">
                            <x-logo class="h-full w-full object-cover" />
                        </div>
                    </div>
                    @if(theme('logo_display', 'logo-and-name') !== 'logo-only')
                        <div class="flex flex-col leading-none">
                            <span class="text-base font-bold tracking-tight" style="font-family: 'Space Grotesk', sans-serif;">
                                {{ config('app.name') }}
                            </span>
                            <span class="text-[9px] uppercase tracking-widest text-muted">Secure Network</span>
                        </div>
                    @endif
                </a>

                {{-- Desktop nav links --}}
                <div class="hidden lg:flex items-center gap-0.5 bg-background/50 rounded-xl p-1">
                    @foreach (\App\Classes\Navigation::getLinks() as $nav)
                        @if (isset($nav['children']) && count($nav['children']) > 0)
                            <x-dropdown>
                                <x-slot:trigger>
                                    <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium text-muted hover:text-base rounded-lg transition-colors">
                                        {{ $nav['name'] }}
                                        <svg class="h-3.5 w-3.5 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </x-slot:trigger>
                                <x-slot:content>
                                    @foreach ($nav['children'] as $child)
                                        <x-navigation.link :href="$child['url']" :spa="isset($child['spa']) ? $child['spa'] : true">
                                            {{ $child['name'] }}
                                        </x-navigation.link>
                                    @endforeach
                                </x-slot:content>
                            </x-dropdown>
                        @else
                            <x-navigation.link
                                :href="$nav['url']"
                                :spa="isset($nav['spa']) ? $nav['spa'] : true"
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-all"
                            >
                                {{ $nav['name'] }}
                            </x-navigation.link>
                        @endif
                    @endforeach
                </div>

                {{-- Right: cart + locale + user --}}
                <div class="flex items-center gap-2">
                    <livewire:components.cart />

                    <div class="hidden md:flex items-center gap-1">
                        <livewire:components.locale-switch />
                        <x-theme-toggle />
                    </div>

                    @if(auth()->check())
                        <livewire:components.notifications />

                        {{-- Desktop user dropdown --}}
                        <div class="hidden lg:flex">
                            <x-dropdown :shift="true">
                                <x-slot:trigger>
                                    <button class="flex items-center gap-2 p-1 rounded-xl hover:bg-neutral/30 transition-colors">
                                        <img src="{{ auth()->user()->avatar }}"
                                             class="size-8 rounded-lg border border-neutral/50 bg-background"
                                             alt="avatar" />
                                        <svg class="h-3.5 w-3.5 text-muted hidden xl:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </x-slot:trigger>
                                <x-slot:content>
                                    <div class="px-3 py-2.5 border-b border-neutral/50 mb-1">
                                        <p class="text-sm font-semibold text-base">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-muted truncate">{{ auth()->user()->email }}</p>
                                    </div>
                                    @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                                        <x-navigation.link :href="$nav['url']" :spa="isset($nav['spa']) ? $nav['spa'] : true">
                                            {{ $nav['name'] }}
                                        </x-navigation.link>
                                    @endforeach
                                    <div class="border-t border-neutral/50 mt-1 pt-1">
                                        <livewire:auth.logout />
                                    </div>
                                </x-slot:content>
                            </x-dropdown>
                        </div>
                    @else
                        <div class="hidden lg:flex items-center gap-2">
                            <a href="{{ route('login') }}" wire:navigate>
                                <x-button.secondary>{{ __('navigation.login') }}</x-button.secondary>
                            </a>
                            @if(!config('settings.registration_disabled', false))
                                <a href="{{ route('register') }}" wire:navigate>
                                    <x-button.primary>{{ __('navigation.register') }}</x-button.primary>
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Mobile hamburger --}}
                    <button
                        @click="slideOverOpen = !slideOverOpen"
                        class="lg:hidden p-2 rounded-xl bg-background/50 hover:bg-neutral/30 transition-colors"
                        aria-label="Toggle menu"
                    >
                        <span x-show="!slideOverOpen"
                              x-transition:enter="transition duration-200"
                              x-transition:enter-start="opacity-0 scale-75"
                              x-transition:enter-end="opacity-100 scale-100"
                              x-transition:leave="transition duration-150"
                              x-transition:leave-start="opacity-100 scale-100"
                              x-transition:leave-end="opacity-0 scale-75"
                              class="flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </span>
                        <span x-show="slideOverOpen"
                              x-transition:enter="transition duration-200"
                              x-transition:enter-start="opacity-0 scale-75"
                              x-transition:enter-end="opacity-100 scale-100"
                              x-transition:leave="transition duration-150"
                              x-transition:leave-start="opacity-100 scale-100"
                              x-transition:leave-end="opacity-0 scale-75"
                              class="flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            {{-- Mobile slide-over --}}
            <template x-teleport="body">
                <div
                    x-show="slideOverOpen"
                    @keydown.window.escape="slideOverOpen = false"
                    x-cloak
                    class="fixed left-0 right-0 top-[68px] z-[99] px-4"
                    style="height: calc(100dvh - 68px);"
                >
                    <div
                        x-show="slideOverOpen"
                        @click.away="slideOverOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="rounded-2xl bg-background-secondary/95 backdrop-blur-xl border border-neutral/50
                               shadow-2xl overflow-y-auto flex flex-col max-h-[calc(100dvh-80px)]"
                    >
                        <div class="p-3 flex-1">
                            <x-navigation.sidebar-links />
                        </div>

                        <div class="p-4 border-t border-neutral/50 space-y-2">
                            <div class="flex items-center justify-end mb-3">
                                <x-theme-toggle />
                            </div>

                            @if(auth()->check())
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-background/50 border border-neutral/50">
                                    <img src="{{ auth()->user()->avatar }}"
                                         class="size-10 rounded-lg border border-neutral/50"
                                         alt="avatar" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-muted truncate">{{ auth()->user()->email }}</p>
                                    </div>
                                </div>
                                @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                                    <x-navigation.link :href="$nav['url']" :spa="isset($nav['spa']) ? $nav['spa'] : true"
                                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl">
                                        {{ $nav['name'] }}
                                    </x-navigation.link>
                                @endforeach
                                <livewire:auth.logout />
                            @else
                                <a href="{{ route('register') }}" wire:navigate>
                                    <x-button.primary class="w-full justify-center">{{ __('navigation.register') }}</x-button.primary>
                                </a>
                                <a href="{{ route('login') }}" wire:navigate>
                                    <x-button.secondary class="w-full justify-center">{{ __('navigation.login') }}</x-button.secondary>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</nav>
