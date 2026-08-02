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
@endphp

<div class="flex flex-row items-center pb-4">
    @if (!empty($breadcrumbs))
        @foreach ($breadcrumbs as $index => $breadcrumb)
            @if ($index > 0)
                <x-ri-arrow-right-s-line class="size-4 text-base mx-2" />
            @endif

            @if (count($breadcrumbs) === 1)
                <span class="text-2xl font-bold">
                    {{ $breadcrumb['name'] ?? '' }}
                </span>
            @elseif ($index === count($breadcrumbs) - 1)
                <span class="text-base/80 font-semibold">
                    {{ $breadcrumb['name'] ?? '' }}
                </span>
            @else
                <a href="{{ isset($breadcrumb['route']) ? route($breadcrumb['route'], $breadcrumb['params'] ?? []) : '#' }}" 
                   class="text-lg font-bold hover:text-primary">
                    {{ $breadcrumb['name'] ?? '' }}
                </a>
            @endif
        @endforeach
    @else
        <span class="text-lg font-bold">{{ __('navigation.home') }}</span>
    @endif
</div>