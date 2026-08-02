<div class="flex justify-center">
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex gap-2 items-center">
            <span>
                @if ($paginator->onFirstPage())
                    <span class="bg-background-secondary text-white px-4 py-2 rounded-lg cursor-not-allowed">Previous</span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="bg-background-secondary text-white px-4 py-2 rounded-lg">Previous</button>
                @endif
            </span>

            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if (
                            $page == $paginator->currentPage() ||
                                $page <= 2 ||
                                $page > $paginator->lastPage() - 2 ||
                                abs($paginator->currentPage() - $page) <= 1)
                            <span>
                                <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled"
                                    class="{{ $page === $paginator->currentPage() ? 'bg-background-secondary/30 text-white' : 'bg-background-secondary text-white' }} px-4 py-2 rounded-lg cursor-pointer">{{ $page }}</button>
                            </span>
                        @elseif($page == 3 || $page == $paginator->lastPage() - 3)
                            <span class="bg-background-secondary text-white px-4 py-2 rounded-lg">
                                <span>...</span>
                            </span>
                        @endif
                    @endforeach
                @else
                    <span class="bg-background-secondary text-white px-4 py-2 rounded-lg">
                        <span>...</span>
                    </span>
                @endif
            @endforeach


            <span>
                @if ($paginator->onLastPage())
                    <span class="bg-background-secondary text-white px-4 py-2 rounded-lg cursor-not-allowed">Next</span>
                @else
                    <button wire:click="nextPage" wire:loading.attr="disabled" rel="next"
                        class="bg-background-secondary text-white px-4 py-2 rounded-lg">Next</button>
                @endif
            </span>
        </nav>
    @endif
</div>
