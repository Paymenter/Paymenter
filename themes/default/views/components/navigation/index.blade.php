<nav class="w-full px-4 lg:px-8 bg-background-secondary border-b border-neutral md:h-16 flex md:flex-row flex-col justify-between fixed top-0 z-20">
    <div
        x-data="{ 
            slideOverOpen: false,
            hasAside: !!document.getElementById('main-aside')
        }"
        x-init="$watch('slideOverOpen', value => { document.documentElement.style.overflow = value ? 'hidden' : '' })"
        class="relative z-50 w-full h-auto">
        <div
            class="flex flex-row items-center justify-between h-16"
            :class="hasAside ? 'w-full' : 'container'">

            <div class="flex flex-row items-center">
                <a href="{{ route('home') }}" class="flex flex-row items-center h-10 gap-2" wire:navigate>
                    <x-logo class="h-8" />
                    @if(theme('logo_display', 'logo-and-name') != 'logo-only')
                    <span class="text-xl font-bold leading-none flex items-center">{{ config('app.name') }}</span>
                    @endif
                </a>
                <div class="md:flex hidden flex-row ml-6">
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
                                    :href="$child['url']"
                                    :spa="isset($child['spa']) ? $nav['spa'] : true">
                                    {{ $child['name'] }}
                                </x-navigation.link>
                                @endforeach
                            </x-slot:content>
                        </x-dropdown>
                    </div>
                    @else
                    <x-navigation.link
                        :href="$nav['url']"
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
                <livewire:components.cart />

                <div class="items-center hidden md:flex mr-1">
                    <x-dropdown>
                        <x-slot:trigger>
                            <div class="flex flex-col">
                                <span class="text-sm text-base font-semibold text-nowrap">{{ strtoupper(app()->getLocale()) }} <span class="text-base/50 font-semibold">|</span> {{ \App\Models\Currency::find(session('currency', config('settings.default_currency')))?->name ?? session('currency', config('settings.default_currency')) }}</span>
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

                @if(auth()->check())
                <livewire:components.notifications />
                <div class="hidden lg:flex">
                    <x-dropdown :showArrow="false">
                        <x-slot:trigger>
                            <img src="{{ auth()->user()->avatar }}" class="size-8 rounded-full border border-neutral bg-background" alt="avatar" />
                        </x-slot:trigger>
                        <x-slot:content>
                            <div class="flex flex-col p-2">
                                <span class="text-sm text-base break-words">{{ auth()->user()->name }}</span>
                                <span class="text-sm text-base break-words">{{ auth()->user()->email }}</span>
                            </div>
                            @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                            <x-navigation.link :href="$nav['url']" :spa="isset($nav['spa']) ? $nav['spa'] : true">
                                {{ $nav['name'] }}
                            </x-navigation.link>
                            @endforeach
                            <livewire:auth.logout />
                        </x-slot:content>
                    </x-dropdown>
                </div>
                @else
                <div class="hidden lg:flex flex-row gap-3">
                    <a href="{{ route('login') }}" wire:navigate>
                        <x-button.secondary>
                            {{ __('navigation.login') }}
                        </x-button.secondary>
                    </a>
                    @if(!config('settings.registration_disabled', false))
                    <a href="{{ route('register') }}" wire:navigate>
                        <x-button.primary>
                            {{ __('navigation.register') }}
                        </x-button.primary>
                    </a>
                    @endif
                </div>
                @endif
                <button
                    @click="slideOverOpen = !slideOverOpen"
                    class="relative w-10 h-10 flex lg:hidden items-center justify-center rounded-lg hover:bg-neutral transition"
                    aria-label="Toggle Menu">

                    <span
                        x-show="!slideOverOpen"
                        x-transition:enter="transition duration-300"
                        x-transition:enter-start="opacity-0 -rotate-90 scale-75"
                        x-transition:enter-end="opacity-100 rotate-0 scale-100"
                        x-transition:leave="transition duration-150"
                        x-transition:leave-start="opacity-100 rotate-0 scale-100"
                        x-transition:leave-end="opacity-0 rotate-90 scale-75"
                        class="absolute inset-0 flex items-center justify-center"
                        aria-hidden="true">
                        <x-ri-menu-fill class="size-5" />
                    </span>

                    <span
                        x-show="slideOverOpen"
                        x-transition:enter="transition duration-300"
                        x-transition:enter-start="opacity-0 rotate-90 scale-75"
                        x-transition:enter-end="opacity-100 rotate-0 scale-100"
                        x-transition:leave="transition duration-150"
                        x-transition:leave-start="opacity-100 rotate-0 scale-100"
                        x-transition:leave-end="opacity-0 -rotate-90 scale-75"
                        class="absolute inset-0 flex items-center justify-center"
                        aria-hidden="true">
                        <x-ri-close-fill class="size-5" />
                    </span>

                </button>
            </div>
        </div>
        <template x-teleport="body">
            <div
                x-show="slideOverOpen"
                @keydown.window.escape="slideOverOpen=false"
                x-cloak
                class="fixed left-0 right-0 top-16 w-full z-[99]"
                style="height:calc(100dvh - 4rem);"
                aria-modal="true"
                tabindex="-1">
                <div
                    x-show="slideOverOpen"
                    @click.away="slideOverOpen = false"
                    x-transition.opacity.duration.300ms
                    class="absolute inset-0 bg-background-secondary border-t border-neutral shadow-lg overflow-y-auto flex flex-col">

                    <div class="flex flex-col h-full p-4">
                        <div class="flex-1 min-h-0 overflow-y-auto">
                            <x-navigation.sidebar-links />
                        </div>
                        <div class="mt-5">
                            @if(auth()->check())

                            <div
                                x-data="{ userPanelOpen: false }"
                                @keydown.escape.window="userPanelOpen = false"
                                x-cloak
                                class="relative">

                                <button @click="userPanelOpen = true" aria-label="Open user menu" class="flex gap-4 items-center justify-start">
                                    <img src="{{ auth()->user()->avatar }}" class="size-10 rounded-full border border-neutral bg-background" alt="avatar" />
                                    <div class="flex flex-col items-start gap-0.5">
                                        <span class="font-bold text-md">{{ auth()->user()->name }}</span>
                                        <span class="text-sm text-base/70">{{ auth()->user()->email }}</span>
                                    </div>
                                </button>

                                <div
                                    x-show="userPanelOpen"
                                    x-transition:enter="transition-opacity ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-60"
                                    x-transition:leave="transition-opacity ease-in duration-200"
                                    x-transition:leave-start="opacity-60"
                                    x-transition:leave-end="opacity-0"
                                    @click="userPanelOpen=false"
                                    class="fixed inset-0 bg-primary/5 backdrop-blur-xs z-40"
                                    style="pointer-events: auto"></div>

                                <div
                                    x-show="userPanelOpen"
                                    x-transition:enter="transition transform ease-out duration-300"
                                    x-transition:enter-start="translate-y-full opacity-0"
                                    x-transition:enter-end="translate-y-0 opacity-100"
                                    x-transition:leave="transition transform ease-in duration-200"
                                    x-transition:leave-start="translate-y-0 opacity-100"
                                    x-transition:leave-end="translate-y-full opacity-0"
                                    class="fixed bottom-0 left-0 right-0 z-50 mx-auto w-full"
                                    style="pointer-events: auto"
                                    @click.away="userPanelOpen = false"
                                    tabindex="-1"
                                    aria-modal="true">
                                    <div class="bg-background-secondary shadow-lg rounded-t-2xl border border-neutral p-6">
                                        <div class="flex gap-4 items-center justify-start">
                                            <img src="{{ auth()->user()->avatar }}" class="size-12 rounded-full border border-neutral bg-background" alt="avatar" />
                                            <div class="flex flex-col gap-0.5">
                                                <span class="font-bold text-lg">{{ auth()->user()->name }}</span>
                                                <span class="text-sm text-base/70">{{ auth()->user()->email }}</span>
                                            </div>
                                        </div>
                                        <div class="h-px w-full bg-neutral my-6"></div>
                                        <div class="mt-4 flex flex-col gap-2 w-full">
                                            @foreach (\App\Classes\Navigation::getAccountDropdownLinks() as $nav)
                                            <x-navigation.link :href="$nav['url']" :spa="isset($nav['spa']) ? $nav['spa'] : true">
                                                {{ $nav['name'] }}
                                            </x-navigation.link>
                                            @endforeach
                                            <livewire:auth.logout />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @else
                            <div class="flex flex-col gap-3 mb-3">
                                @if(!config('settings.registration_disabled', false))
                                <a href="{{ route('register') }}" wire:navigate>
                                    <x-button.primary>
                                        {{ __('navigation.register') }}
                                    </x-button.primary>
                                </a>
                                @endif
                                <a href="{{ route('login') }}" wire:navigate>
                                    <x-button.secondary>
                                        {{ __('navigation.login') }}
                                    </x-button.secondary>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</nav>
