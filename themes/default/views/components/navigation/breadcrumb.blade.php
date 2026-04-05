@php
    $currentRoute = request()->livewireUrl();

    $navigation = [
        \App\Classes\Navigation::getLinks(),
        \App\Classes\Navigation::getAccountDropdownLinks(),
        \App\Classes\Navigation::getDashboardLinks(),
    ];

    function findBreadcrumb($items, $currentRoute) {
        foreach ($items as $item) {
            if (isset($item['url']) && $item['url'] === $currentRoute) {
                return [$item];
            }

            if (!empty($item['children'])) {
                $childTrail = findBreadcrumb($item['children'], $currentRoute);
                if (!empty($childTrail)) {
                    return array_merge([$item], $childTrail);
                }
            }
        }

        return [];
    }

    $breadcrumbs = [];
    foreach ($navigation as $group) {
        $breadcrumbs = findBreadcrumb($group, $currentRoute);
        if (!empty($breadcrumbs)) {
            break;
        }
    }
    
    // FIX: Remove home from breadcrumbs to prevent duplication with the hardcoded home link
    $breadcrumbs = array_values(array_filter($breadcrumbs, function($item) {
        return !(isset($item['url']) && $item['url'] === route('home'));
    }));
    
    // Check if we're on the home page
    $isHomePage = $currentRoute === route('home');
@endphp

{{-- Breadcrumb Container --}}
<nav class="flex items-center py-4 px-1 overflow-x-auto scrollbar-hide" aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-1 text-sm">
        
        {{-- Only show Home link if we're NOT on the home page --}}
        @if(!$isHomePage)
            <li class="flex items-center">
                <a href="{{ route('home') }}" 
                   wire:navigate
                   class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200 group">
                    <x-ri-home-4-line class="size-4 group-hover:scale-110 transition-transform duration-200" />
                    <span class="hidden sm:inline font-medium">{{ __('navigation.home') }}</span>
                </a>
            </li>
        @endif
        
        {{-- Breadcrumb Items --}}
        @if (!empty($breadcrumbs))
            @foreach ($breadcrumbs as $index => $breadcrumb)
                {{-- Separator (only if we're not on home page or if there are breadcrumbs) --}}
                @if(!$isHomePage || $index > 0)
                    <li class="flex items-center text-gray-400 dark:text-gray-600">
                        <x-ri-arrow-right-s-line class="size-4" />
                    </li>
                @endif
                
                {{-- Breadcrumb Item --}}
                <li class="flex items-center">
                    @if ($index === count($breadcrumbs) - 1)
                        {{-- Current Page (Active) --}}
                        <div class="flex items-center gap-2">
                            @if(isset($breadcrumb['icon']))
                                <x-dynamic-component :component="$breadcrumb['icon']" 
                                    class="size-4 text-primary-600 dark:text-primary-400" />
                            @endif
                            <span class="font-semibold text-gray-900 dark:text-white bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-400 bg-clip-text text-transparent">
                                {{ $breadcrumb['name'] ?? '' }}
                            </span>
                        </div>
                    @else
                        {{-- Parent Page (Link) --}}
                        <a href="{{ isset($breadcrumb['route']) ? route($breadcrumb['route'], $breadcrumb['params'] ?? []) : (isset($breadcrumb['url']) ? $breadcrumb['url'] : '#') }}" 
                           @if(isset($breadcrumb['spa']) && $breadcrumb['spa']) wire:navigate @endif
                           class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
                            @if(isset($breadcrumb['icon']))
                                <x-dynamic-component :component="$breadcrumb['icon']" 
                                    class="size-4" />
                            @endif
                            <span>{{ $breadcrumb['name'] ?? '' }}</span>
                        </a>
                    @endif
                </li>
            @endforeach
        @elseif($isHomePage)
            {{-- On Home Page - Show just the home title --}}
            <li class="flex items-center">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('navigation.home') }}
                </span>
            </li>
        @endif
    </ol>
</nav>

{{-- Optional: Page Title Section (for better visual hierarchy) --}}
@if(!empty($breadcrumbs) && !$isHomePage)
    <div class="mt-2 pb-3 border-b border-gray-200 dark:border-gray-800">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
            {{ end($breadcrumbs)['name'] ?? '' }}
        </h1>
        @if(isset(end($breadcrumbs)['description']))
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ end($breadcrumbs)['description'] }}
            </p>
        @endif
    </div>
@endif