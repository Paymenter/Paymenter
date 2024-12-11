<div class="relative w-full">
    <label for="{{ $id ?? 'select' }}" class="sr-only">{{ $label ?? '' }}</label>
    
    <select
        id="{{ $id ?? 'select' }}"
        name="{{ $name ?? '' }}"
        class="relative block w-full min-h-[38px] py-2 pl-3 pr-10 text-left bg-secondary border border-neutral rounded-md shadow-sm cursor-pointer text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral-800 appearance-none"
        {{ $attributes }}
    >
        {{ $slot }}
    </select>
    
    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-4 w-4 text-base">
            <path d="M18.2072 9.0428 12.0001 2.83569 5.793 9.0428 7.20721 10.457 12.0001 5.66412 16.793 10.457 18.2072 9.0428ZM5.79285 14.9572 12 21.1643 18.2071 14.9572 16.7928 13.543 12 18.3359 7.20706 13.543 5.79285 14.9572Z"></path>
        </svg>
    </span>
</div>
