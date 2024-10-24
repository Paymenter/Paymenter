@props(['href', 'spa' => true])
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex flex-row items-center p-3 gap-2 text-sm ' . ($href === request()->url() ? 'text-secondary' : 'text-white hover:text-primary-500')]) }} @if($spa) wire:navigate @endif>
    {{ $slot }}
</a>