<a href="{{ $href }}" class="flex flex-row items-center p-3 gap-2 text-sm @if($href === request()->url()) text-secondary @else text-white hover:text-primary-500 @endif">
    {{ $slot }}
</a>