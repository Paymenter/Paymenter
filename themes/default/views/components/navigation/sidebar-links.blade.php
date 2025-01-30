<div class="px-4 py-6 flex flex-col gap-2">
    @foreach (\App\Classes\Navigation::get() as $groupName => $group)
    @foreach ($group['items'] as $nav)
    @if (isset($nav['children']) && count($nav['children']) > 0)
    <div x-data="{ 
                activeAccordion: '', 
                setActiveAccordion(id) { 
                    this.activeAccordion = (this.activeAccordion == id) ? '' : id 
                } 
            }" class="relative w-full mx-auto overflow-hidden text-sm font-normal divide-y divide-gray-200">
        <div x-data="{ id: $id('accordion') }" class="cursor-pointer">
            <button @click="setActiveAccordion(id)"
                class="flex items-center justify-between w-full p-3 text-sm font-semibold whitespace-nowrap rounded-lg hover:bg-primary/10">
                <div class="flex flex-row gap-2">
                    @if (isset($nav['icon']))
                    <x-dynamic-component :component="$nav['icon']"
                        class="{{ isset($nav['active']) && $nav['active'] ? 'w-5 h-5 text-primary' : 'w-5 h-5 hover:text-base/80' }}" />
                    @endif
                    <span>{{ $nav['name'] }}</span>
                </div>
                <x-ri-arrow-down-s-line x-bind:class="{ 'rotate-180': activeAccordion==id }"
                    class="h-4 w-4 text-base ease-out duration-300" />
            </button>
            <div x-show="activeAccordion==id" x-collapse x-cloak>
                <div class="p-4 pt-0 opacity-70">
                    @foreach ($nav['children'] as $child)
                    <div class="flex items-center space-x-2">
                        <x-navigation.link :href="route($child['route'], $child['params'] ?? null)"
                            :spa="isset($child['spa']) ? $child['spa'] : true">
                            {{ $child['name'] }}
                        </x-navigation.link>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @else
    <div
        class="flex items-center rounded-lg {{ isset($nav['active']) && $nav['active'] ? 'bg-primary/10' : 'hover:bg-primary/10' }}">
        <x-navigation.link :href="route($nav['route'], $nav['params'] ?? null)"
            :spa="isset($nav['spa']) ? $nav['spa'] : true" class="w-full">
            @if (isset($nav['icon']))
            <x-dynamic-component :component="$nav['icon']"
                class="{{ isset($nav['active']) && $nav['active'] ? 'w-5 h-5 text-primary' : 'w-5 h-5 hover:text-base/80' }}" />
            @endif
            {{ $nav['name'] }}
        </x-navigation.link>
    </div>
    @endif
    @endforeach

    @if($group['separator'])
    <div class="h-px w-full bg-neutral"></div>
    @endif
    @endforeach
</div>