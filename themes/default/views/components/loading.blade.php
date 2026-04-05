@props([
    'target' => null,
    'size' => 'md', // sm, md, lg, xl
    'color' => 'primary', // primary, white, gray
    'overlay' => false,
    'text' => 'Loading...',
    'fullscreen' => false
])

@php
    $sizes = [
        'sm' => 'size-4',
        'md' => 'size-6',
        'lg' => 'size-8',
        'xl' => 'size-12',
    ];
    
    $colors = [
        'primary' => 'fill-primary-600 dark:fill-primary-400',
        'white' => 'fill-white',
        'gray' => 'fill-gray-600 dark:fill-gray-400',
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $colorClass = $colors[$color] ?? $colors['primary'];
@endphp

<div role="status" 
    @if(!$target) wire:loading @else wire:loading wire:target="{{ $target }}" @endif
    class="{{ $overlay ? 'fixed inset-0 flex items-center justify-center bg-black/20 backdrop-blur-sm z-50' : 'inline-flex items-center gap-2' }}"
>
    @if($overlay)
        <div class="flex flex-col items-center gap-3 p-6 bg-white dark:bg-gray-900 rounded-2xl shadow-xl">
            <x-ri-loader-5-fill 
                aria-hidden="true" 
                class="{{ $sizeClass }} {{ $colorClass }} animate-spin-slow" 
            />
            @if($text)
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $text }}</p>
            @endif
        </div>
    @elseif($fullscreen)
        <div class="fixed inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm z-50">
            <div class="flex flex-col items-center gap-3">
                <x-ri-loader-5-fill 
                    aria-hidden="true" 
                    class="{{ $sizeClass }} {{ $colorClass }} animate-spin-slow" 
                />
                @if($text)
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $text }}</p>
                @endif
            </div>
        </div>
    @else
        <div class="flex items-center gap-2">
            <x-ri-loader-5-fill 
                aria-hidden="true" 
                class="{{ $sizeClass }} {{ $colorClass }} animate-spin-slow" 
            />
            @if($text)
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $text }}</span>
            @endif
        </div>
    @endif
    
    <span class="sr-only">Loading...</span>
</div>