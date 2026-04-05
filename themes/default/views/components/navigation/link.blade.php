@props(['href', 'spa' => true])

<a 
    href="{{ $href }}" 
    {{ $attributes->merge(['class' => 'group relative flex flex-row items-center px-3 py-2.5 gap-2.5 text-sm font-medium rounded-lg transition-all duration-200 ' . ($href === request()->url() ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/30' : 'text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800')]) }} 
    @if($spa) wire:navigate @endif
>
    {{-- Icon slot (optional) --}}
    @if(isset($icon))
        <div class="flex-shrink-0 transition-transform duration-200 group-hover:scale-105">
            {{ $icon }}
        </div>
    @endif

    {{-- Main content --}}
    <span class="relative">
        {{ $slot }}
        
        {{-- Active indicator underline --}}
        @if($href === request()->url())
            <span class="absolute -bottom-1 left-0 right-0 h-0.5 bg-primary-500 dark:bg-primary-400 rounded-full"></span>
        @endif
    </span>

    {{-- Hover arrow for external feeling (optional) --}}
    @if(!$spa && $attributes->has('target') && $attributes->get('target') === '_blank')
        <svg class="w-3.5 h-3.5 opacity-0 -mr-2 transition-all duration-200 group-hover:opacity-100 group-hover:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
        </svg>
    @endif
</a>