<div class="flex flex-col h-full p-3 gap-1">
    @foreach (\App\Classes\Navigation::getDashboardLinks() as $nav)
        @if (!empty($nav['children']))
            <div x-data="{ open: {{ $nav['active'] ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl text-sm font-medium
                           {{ $nav['active'] ? 'bg-primary/10 text-primary border border-primary/20' : 'text-muted hover:bg-neutral/30 hover:text-base' }}
                           transition-all">
                    <div class="flex items-center gap-2.5">
                        @isset($nav['icon'])
                            <x-dynamic-component :component="$nav['icon']" class="size-4 flex-shrink-0" />
                        @endisset
                        <span>{{ $nav['name'] }}</span>
                    </div>
                    <svg class="size-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse x-cloak class="mt-1 ml-4 pl-3 border-l border-neutral/50 flex flex-col gap-0.5">
                    @foreach ($nav['children'] as $child)
                        @if ($child['condition'] ?? true)
                            <x-navigation.link :href="$child['url']" :spa="$child['spa'] ?? true"
                                class="px-3 py-1.5 rounded-lg text-sm {{ $child['active'] ? 'text-primary font-semibold' : 'text-muted hover:text-base' }} transition-colors">
                                {{ $child['name'] }}
                            </x-navigation.link>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <x-navigation.link :href="$nav['url']" :spa="$nav['spa'] ?? true"
                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium
                       {{ $nav['active'] ? 'bg-primary/10 text-primary border border-primary/20' : 'text-muted hover:bg-neutral/30 hover:text-base' }}
                       transition-all">
                @isset($nav['icon'])
                    <x-dynamic-component :component="$nav['icon']" class="size-4 flex-shrink-0" />
                @endisset
                {{ $nav['name'] }}
            </x-navigation.link>
        @endif
        @isset($nav['separator'])
            <div class="my-1 h-px bg-neutral/50"></div>
        @endisset
    @endforeach
</div>
