@props(['extension'])

@php
    $price = $extension['price'] > 0 ? '$' . number_format($extension['price'], 2) : 'Free';
    $author = $extension['author'] === 'CorwinDev' ? 'Paymenter' : $extension['author'];
    $downloads = $extension['download_count'] >= 1000 ? round($extension['download_count'] / 1000, 1) . 'k' : $extension['download_count'];
@endphp

<a href="{{ $extension['url'] }}" target="_blank" rel="noopener noreferrer"
    class="flex flex-col overflow-hidden transition-all duration-300 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 hover:shadow-lg hover:border-primary-500 dark:hover:border-primary-500">
    
    <div class="relative h-48 bg-gray-200 dark:bg-gray-700">
        <img src="{{ $extension['image_url'] }}" alt="{{ $extension['name'] }}" class="object-cover w-full h-full">
        <span class="absolute px-2 py-1 text-xs font-semibold text-white capitalize rounded-full top-2 right-2 bg-primary-600">
            {{ $extension['type'] }}
        </span>
    </div>
    
    <div class="flex flex-col flex-grow p-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $extension['name'] }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">By {{ $author }}</p>
        
        <p class="flex-grow mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
            {{ $extension['tag_line'] }}
        </p>
        
        <div class="flex items-center justify-between pt-3 mt-4 text-sm text-gray-500 border-t border-gray-200 dark:border-gray-700 dark:text-gray-400">
            <div class="flex items-center space-x-4">
                @if($extension['review_count'] > 0)
                <div class="flex items-center gap-1" title="Rating">
                    <x-ri-star-fill class="w-4 h-4 text-yellow-400" />
                    <span>{{ $extension['review_average'] }} ({{ $extension['review_count'] }})</span>
                </div>
                @endif
                <div class="flex items-center gap-1" title="Downloads">
                    <x-ri-download-fill class="w-4 h-4" />
                    <span>{{ $downloads }}</span>
                </div>
            </div>
            <div class="text-lg font-bold text-gray-800 dark:text-gray-200">
                {{ $price }}
            </div>
        </div>
    </div>
</a>
