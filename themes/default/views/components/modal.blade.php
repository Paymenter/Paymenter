@props([
    'title' => '',
    'closable' => true,
    'closeTrigger' => '',
    'open' => false,
    'width' => 'max-w-3xl'
])
<div x-data="{ open: {{ $open ? 'true' : 'false' }} }">
    <template x-teleport="body">
        <div class="fixed inset-0 z-30 flex items-center justify-center overflow-auto bg-black bg-opacity-50"
            x-show="open">
            <!-- Modal inner -->
            <div class="px-6 py-4 w-full mx-2 md:mx-auto text-left bg-primary-800 rounded shadow-lg {{ $width }}" x-cloak
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-semibold text-primary-100">{{ $title }}</h2>
                    @if ($closable && !$closeTrigger)
                        <button @click="open = false" class="text-primary-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @elseif ($closable && $closeTrigger)
                        {{ $closeTrigger }}
                    @endif
                </div>
                <div class="mt-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>

</div>
