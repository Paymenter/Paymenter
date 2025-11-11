<x-filament-panels::page>
    <div class="border-b border-gray-200 dark:border-white/10">
        <nav class="flex -mb-px space-x-8" aria-label="Tabs">
            <button
                wire:click="$set('activeTab', 'marketplace')"
                @class([
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                    'border-primary-500 text-primary-600' => $activeTab === 'marketplace',
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600' => $activeTab !== 'marketplace',
                ])>
                Browse Marketplace
            </button>
            <button
                wire:click="$set('activeTab', 'installable')"
                @class([
                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                    'border-primary-500 text-primary-600' => $activeTab === 'installable',
                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600' => $activeTab !== 'installable',
                ])>
                Ready to Install / Upload
            </button>
        </nav>
    </div>
    @if ($activeTab === 'marketplace')
        <div class="">
            <div class="flex flex-col gap-4">
                <div class="relative">
                    <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-3"><x-ri-search-line class="w-5 h-5 text-gray-400" /></div>
                    <input type="search" placeholder="Search extensions by name..." wire:model.live.debounce.500ms="search" class="block w-full p-3 border-gray-300 rounded-lg shadow-sm ps-10 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex items-center space-x-2">
                    @php
                        $baseClasses = 'px-4 py-2 text-sm font-medium border rounded-lg focus:outline-none transition-colors';
                        $activeClasses = 'bg-primary-600 border-primary-600 text-white hover:bg-primary-700';
                        $inactiveClasses = 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700';
                    @endphp
                    <button wire:click="$set('filter', 'all')" @class([$baseClasses, $activeClasses => $this->filter === 'all', $inactiveClasses => $this->filter !== 'all'])>All</button>
                    <button wire:click="$set('filter', 'extension')" @class([$baseClasses, $activeClasses => $this->filter === 'extension', $inactiveClasses => $this->filter !== 'extension'])>Extensions</button>
                    <button wire:click="$set('filter', 'theme')" @class([$baseClasses, $activeClasses => $this->filter === 'theme', $inactiveClasses => $this->filter !== 'theme'])>Themes</button>
                </div>
            </div>
            <div class="mt-6">
                @if($this->error)
                    <div class="p-4 text-center text-gray-500 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Something went wrong</h3>
                        <p class="mt-2">{{ $this->error }}</p>
                    </div>
                @elseif($this->extensions->isEmpty())
                    <div class="p-4 text-center text-gray-500 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400">
                        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200">No extensions found</h3>
                        <p class="mt-2">Try adjusting your search or filter criteria.</p>
                    </div>
                @else
                    <div>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($this->extensions as $extension)
                                <x-extension-card :extension="$extension" :key="$extension['name']" />
                            @endforeach
                        </div>
                        @if ($this->canLoadMore)
                            <div id="load-more-trigger" x-data="{ init() { const observer = new IntersectionObserver((entries) => { if (entries[0].isIntersecting) { @this.loadMore(); } }, { rootMargin: '200px' }); observer.observe(this.$el); } }" class="flex items-center justify-center w-full h-16">
                                <div wire:loading wire:target="loadMore"><x-filament::loading-indicator class="w-8 h-8" /></div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="">
            {{ $this->table }}
        </div>
    @endif
</x-filament-panels::page>
