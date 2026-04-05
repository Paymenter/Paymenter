<div class="lg:px-4 lg:py-6 flex flex-col gap-3">
    {{-- Mobile Navigation Links --}}
    <div class="flex flex-col gap-1 md:hidden">
        @foreach (\App\Classes\Navigation::getLinks() as $nav)
        @if (!empty($nav['children']))
        <div x-data="{ activeAccordion: {{ $nav['active'] ? 'true' : 'false' }} }"
            class="relative w-full mx-auto overflow-hidden text-sm font-normal">
            <div class="cursor-pointer">
                <button @click="activeAccordion = !activeAccordion"
                    class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div class="flex flex-row items-center gap-3">
                        @isset($nav['icon'])
                            <x-dynamic-component :component="$nav['icon']"
                            class="size-5 transition-colors duration-200 {{ $nav['active'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" />
                        @endisset
                        <span class="{{ $nav['active'] ? 'text-gray-900 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-300' }}">{{ $nav['name'] }}</span>
                    </div>
                    <x-ri-arrow-down-s-line x-bind:class="{ 'rotate-180': activeAccordion }"
                        class="size-4 text-gray-500 dark:text-gray-400 transition-transform duration-300" />
                </button>
                <div x-show="activeAccordion" x-collapse x-cloak>
                    <div class="pl-10 pr-3 py-1 space-y-1">
                        @foreach ($nav['children'] as $child)
                        <div class="flex items-center">
                            <x-navigation.link :href="$child['url']"
                                :spa="$child['spa'] ?? true"
                                class="w-full px-3 py-2 text-sm rounded-lg transition-all duration-200 {{ $child['active'] ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/30 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                {{ $child['name'] }}
                            </x-navigation.link>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center rounded-lg transition-all duration-200 {{ $nav['active'] ? 'bg-primary-50 dark:bg-primary-950/30' : 'hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <x-navigation.link :href="$nav['url']"
                :spa="$nav['spa'] ?? true" 
                class="w-full px-3 py-2.5 gap-3">
                @isset($nav['icon'])
                    <x-dynamic-component :component="$nav['icon']"
                        class="size-5 transition-colors duration-200 {{ $nav['active'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" />
                @endisset
                <span class="{{ $nav['active'] ? 'text-gray-900 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-300' }}">{{ $nav['name'] }}</span>
            </x-navigation.link>
        </div>
        @endif
        @isset($nav['separator'])
        <div class="h-px w-full bg-gray-200 dark:bg-gray-800 my-2"></div>
        @endisset
        @endforeach
    </div>

    {{-- Divider between navigation sections --}}
    <div class="hidden md:block h-px w-full bg-gray-200 dark:bg-gray-800 my-2"></div>

    {{-- Dashboard Links --}}
    <div class="flex flex-col gap-1">
        <div class="px-3 py-2 hidden md:block">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Dashboard</span>
        </div>
        
        @foreach (\App\Classes\Navigation::getDashboardLinks() as $nav)
        @if (!empty($nav['children']))
        <div x-data="{ activeAccordion: {{ $nav['active'] ? 'true' : 'false' }} }"
            class="relative w-full mx-auto overflow-hidden text-sm font-normal">
            <div class="cursor-pointer">
                <button @click="activeAccordion = !activeAccordion"
                    class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div class="flex flex-row items-center gap-3">
                        @isset($nav['icon'])
                            <x-dynamic-component :component="$nav['icon']"
                                class="size-5 transition-colors duration-200 {{ $nav['active'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" />
                        @endisset
                        <span class="{{ $nav['active'] ? 'text-gray-900 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-300' }}">{{ $nav['name'] }}</span>
                    </div>
                    <x-ri-arrow-down-s-line x-bind:class="{ 'rotate-180': activeAccordion }"
                        class="size-4 text-gray-500 dark:text-gray-400 transition-transform duration-300" />
                </button>
                <div x-show="activeAccordion" x-collapse x-cloak>
                    <div class="pl-10 pr-3 py-1 space-y-1">
                        @foreach ($nav['children'] as $child)
                            @if ($child['condition'] ?? true)
                            <div class="flex items-center">
                                <x-navigation.link :href="$child['url']"
                                    :spa="$child['spa'] ?? true"
                                    class="w-full px-3 py-2 text-sm rounded-lg transition-all duration-200 {{ $child['active'] ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/30 font-medium' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                    {{ $child['name'] }}
                                </x-navigation.link>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center rounded-lg transition-all duration-200 {{ $nav['active'] ? 'bg-primary-50 dark:bg-primary-950/30' : 'hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <x-navigation.link :href="$nav['url']"
                :spa="$nav['spa'] ?? true"
                class="w-full px-3 py-2.5 gap-3">
                @isset($nav['icon'])
                    <x-dynamic-component :component="$nav['icon']"
                        class="size-5 transition-colors duration-200 {{ $nav['active'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}" />
                @endisset
                <span class="{{ $nav['active'] ? 'text-gray-900 dark:text-white font-semibold' : 'text-gray-700 dark:text-gray-300' }}">{{ $nav['name'] }}</span>
            </x-navigation.link>
        </div>
        @endif
        @isset($nav['separator'])
        <div class="h-px w-full bg-gray-200 dark:bg-gray-800 my-2"></div>
        @endisset
        @endforeach
    </div>

    {{-- Mobile Footer Actions --}}
    <div class="flex flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-gray-200 dark:border-gray-800 md:hidden">
        <x-dropdown>
            <x-slot:trigger>
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200 text-nowrap px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition cursor-pointer">
                        {{ strtoupper(app()->getLocale()) }} 
                        <span class="text-gray-400 dark:text-gray-600 mx-1">|</span> 
                        {{ session('currency', config('settings.default_currency')) }}
                    </span>
                </div>
            </x-slot:trigger>
            <x-slot:content>
                <strong class="block p-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Language</strong>
                <livewire:components.language-switch />
                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                <strong class="block p-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Currency</strong>
                <livewire:components.currency-switch />
            </x-slot:content>
        </x-dropdown>

        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">Theme</span>
            <x-theme-toggle />
        </div>
    </div>
</div>