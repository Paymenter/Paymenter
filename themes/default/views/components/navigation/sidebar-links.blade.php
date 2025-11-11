<div class="lg:px-4 lg:py-6 flex flex-col gap-2">
    <div class="flex flex-col gap-2 md:hidden">
        @foreach (\App\Classes\Navigation::getLinks() as $nav)
        @if (!empty($nav['children']))
        <div x-data="{ activeAccordion: {{ $nav['active'] ? 'true' : 'false' }} }"
            class="relative w-full mx-auto overflow-hidden text-sm font-normal divide-y divide-gray-200">
            <div class="cursor-pointer">
                <button @click="activeAccordion = !activeAccordion"
                    class="flex items-center justify-between w-full p-3 text-sm font-semibold whitespace-nowrap rounded-lg hover:bg-primary/5">
                    <div class="flex flex-row gap-2">
                        @isset($nav['icon'])
                            <x-dynamic-component :component="$nav['icon']"
                            class="size-5 {{ $nav['active'] ? 'text-primary' : 'fill-base/50' }}" />
                        @endisset
                        <span>{{ $nav['name'] }}</span>
                    </div>
                    <x-ri-arrow-down-s-line x-bind:class="{ 'rotate-180': activeAccordion }"
                        class="size-4 text-base ease-out duration-300" />
                </button>
                <div x-show="activeAccordion" x-collapse x-cloak>
                    <div class="p-4 pt-0 opacity-70">
                        @foreach ($nav['children'] as $child)
                        <div class="flex items-center space-x-2">
                            <x-navigation.link :href="$child['url']"
                                :spa="$child['spa'] ?? true"
                                class="{{ $child['active'] ? 'text-primary font-bold' : '' }}">
                                {{ $child['name'] }}
                            </x-navigation.link>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center rounded-lg {{ $nav['active'] ? 'bg-primary/5' : 'hover:bg-primary/5' }}">
            <x-navigation.link :href="$nav['url']"
                :spa="$nav['spa'] ?? true" class="w-full">
                @isset($nav['icon'])
                    <x-dynamic-component :component="$nav['icon']"
                        class="size-5 {{ $nav['active'] ? 'text-primary' : 'fill-base/50' }}" />
                @endisset
                {{ $nav['name'] }}
            </x-navigation.link>
        </div>
        @endif
        @isset($nav['separator'])
        <div class="h-px w-full bg-neutral"></div>
        @endisset
        @endforeach
    </div>

    <div class="flex flex-col gap-2">
        @foreach (\App\Classes\Navigation::getDashboardLinks() as $nav)
        @if (!empty($nav['children']))
        <div x-data="{ activeAccordion: {{ $nav['active'] ? 'true' : 'false' }} }"
            class="relative w-full mx-auto overflow-hidden text-sm font-normal divide-y divide-gray-200">
            <div class="cursor-pointer">
                <button @click="activeAccordion = !activeAccordion"
                    class="flex items-center justify-between w-full p-3 text-sm font-semibold whitespace-nowrap rounded-lg hover:bg-primary/5">
                    <div class="flex flex-row gap-2">
                        @isset($nav['icon'])
                            <x-dynamic-component :component="$nav['icon']"
                                class="size-5 {{ $nav['active'] ? 'text-primary' : 'fill-base/50' }}" />
                        @endisset
                        <span>{{ $nav['name'] }}</span>
                    </div>
                    <x-ri-arrow-down-s-line x-bind:class="{ 'rotate-180': activeAccordion }"
                        class="size-4 text-base ease-out duration-300" />
                </button>
                <div x-show="activeAccordion" x-collapse x-cloak>
                    <div class="p-4 pt-0 opacity-70">
                        @foreach ($nav['children'] as $child)
                            @if ($child['condition'] ?? true)
                            <div class="flex items-center space-x-2">
                                <x-navigation.link :href="$child['url']"
                                    :spa="$child['spa'] ?? true"
                                    class="{{ $child['active'] ? 'text-primary font-bold' : '' }}">
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
        <div class="flex items-center rounded-lg {{ $nav['active'] ? 'bg-primary/5' : 'hover:bg-primary/5' }}">
            <x-navigation.link :href="$nav['url']"
                :spa="$nav['spa'] ?? true"
                class="w-full">
                @isset($nav['icon'])
                    <x-dynamic-component :component="$nav['icon']"
                        class="size-5 {{ $nav['active'] ? 'text-primary' : 'fill-base/50' }}" />
                @endisset
                {{ $nav['name'] }}
            </x-navigation.link>
        </div>
        @endif
        @isset($nav['separator'])
        <div class="h-px w-full bg-neutral"></div>
        @endisset
        @endforeach
        <div class="flex flex-row items-center mt-4 justify-between md:hidden">
            <x-dropdown>
                <x-slot:trigger>
                    <div class="flex flex-col">
                        <span class="text-sm text-base font-semibold text-nowrap">{{ strtoupper(app()->getLocale()) }} <span class="text-base/50 font-semibold">|</span> {{ session('currency', config('settings.default_currency')) }}</span>
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
    </div>
</div>
