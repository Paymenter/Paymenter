@php
    $currentRoute = request()->livewireUrl();
    $navigation = [
        \App\Classes\Navigation::getLinks(),
        \App\Classes\Navigation::getAccountDropdownLinks(),
        \App\Classes\Navigation::getDashboardLinks(),
    ];
    function findBreadcrumb($items, $currentRoute) {
        foreach ($items as $item) {
            if (isset($item['url']) && $item['url'] === $currentRoute) return [$item];
            if (!empty($item['children'])) {
                $trail = findBreadcrumb($item['children'], $currentRoute);
                if (!empty($trail)) return array_merge([$item], $trail);
            }
        }
        return [];
    }
    $breadcrumbs = [];
    foreach ($navigation as $group) {
        $breadcrumbs = findBreadcrumb($group, $currentRoute);
        if (!empty($breadcrumbs)) break;
    }
@endphp
<div class="flex flex-row items-center pb-4">
    @if (!empty($breadcrumbs))
        @foreach ($breadcrumbs as $index => $breadcrumb)
            @if ($index > 0)<svg class="size-4 text-muted mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>@endif
            @if (count($breadcrumbs) === 1)<span class="text-2xl font-bold">{{ $breadcrumb['name'] ?? '' }}</span>
            @elseif ($index === count($breadcrumbs) - 1)<span class="font-semibold">{{ $breadcrumb['name'] ?? '' }}</span>
            @else<a href="{{ $breadcrumb['url'] ?? '#' }}" wire:navigate class="text-lg font-bold hover:text-primary">{{ $breadcrumb['name'] ?? '' }}</a>
            @endif
        @endforeach
    @else
        <span class="text-2xl font-bold">{{ __('navigation.home') }}</span>
    @endif
</div>
