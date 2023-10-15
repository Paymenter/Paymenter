@props(['paginator'])
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" {{ $attributes->merge(['class' => 'flex items-center']) }}>
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="mr-2 px-2 py-1 text-sm font-medium text-gray-500 bg-secondary-200 rounded-md cursor-not-allowed"
                aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span aria-hidden="true">@lang('pagination.previous')</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                class="mr-2 px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-200 bg-secondary-200 rounded-md hover:bg-secondary-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                @lang('pagination.previous')
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div class="gap-1 flex items-center">
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            {{-- "Three Dots" Separator --}}
            @if ($paginator->currentPage() > 3 && $i === 2)
                <span
                    class="px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-200 bg-secondary-200 rounded-md hover:bg-secondary-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">...</span>
            @endif

            {{-- Array Of Links --}}
            @if ($i == $paginator->currentPage())
                <span
                    class="px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-200 bg-secondary-300 rounded-md">{{ $i }}</span>
            @elseif($i === $paginator->currentPage() + 1 || $i === $paginator->currentPage() + 2)
                <a href="{{ $paginator->url($i) }}"
                    class="px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-200 bg-secondary-200 rounded-md hover:bg-secondary-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ $i }}</a>
            @elseif($i === $paginator->currentPage() - 1 || $i === $paginator->currentPage() - 2)
                <a href="{{ $paginator->url($i) }}"
                    class="px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-200 bg-secondary-200 rounded-md hover:bg-secondary-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ $i }}</a>
            @endif

            {{-- "Three Dots" Separator --}}
            @if ($paginator->currentPage() < $paginator->lastPage() - 2 && $i === $paginator->lastPage() - 1)
                <span
                    class="px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-200 bg-secondary-200 rounded-md hover:bg-secondary-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">...</span>
            @endif
        @endfor
        </div>
        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                class="px-2 ml-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-200 bg-secondary-200 rounded-md hover:bg-secondary-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                @lang('pagination.next')
            </a>
        @else
            <span class="px-2 ml-2 py-1 text-sm font-medium text-gray-500 bg-secondary-200 rounded-md cursor-not-allowed"
                aria-disabled="true" aria-label="@lang('pagination.next')">
                <span aria-hidden="true">@lang('pagination.next')</span>
            </span>
        @endif
    </nav>
@endif
