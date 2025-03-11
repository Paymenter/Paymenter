@props([
    'target' => null,
])
<div role="status" 
    @if(!$target) wire:loading @else wire:loading wire:target="{{ $target }}" @endif
>
    <x-ri-loader-5-fill aria-hidden="true" {{ $attributes->merge(['class' => 'size-6 me-2 fill-background animate-spin']) }} />
    <span class="sr-only">Loading...</span>
</div>