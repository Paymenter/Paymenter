@if($cartCount > 0)
<div x-data="{ 
    cartCount: {{ $cartCount }}, 
    previousCount: {{ $cartCount }},
    animate: false 
}" 
x-init="$watch('cartCount', value => {
    if (value > previousCount) {
        animate = true;
        setTimeout(() => animate = false, 500);
    }
    previousCount = value;
})"
class="relative group">
    
    <x-navigation.link 
        :href="route('cart')" 
        class="relative size-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700 hover:border-primary-400 dark:hover:border-primary-500 transition-all duration-300 group"
    >
        <x-ri-shopping-bag-4-fill class="size-5 text-gray-600 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-300" />
        
        {{-- Animated Cart Badge --}}
        <div 
            x-effect="cartCount = {{ $cartCount }}"
            class="absolute -top-2 -right-2 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[11px] font-bold text-white bg-gradient-to-r from-primary-500 to-primary-600 dark:from-primary-500 dark:to-primary-600 rounded-full shadow-lg shadow-primary-500/30 dark:shadow-primary-600/30 ring-2 ring-white dark:ring-gray-900"
            :class="{ 'animate-bounce scale-110': animate }"
            x-text="cartCount > 99 ? '99+' : cartCount"
        >
            {{ $cartCount > 99 ? '99+' : $cartCount }}
        </div>
    </x-navigation.link>
    
    {{-- Enhanced Tooltip --}}
    <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none whitespace-nowrap transform translate-y-2 group-hover:translate-y-0">
        <div class="bg-gray-900 dark:bg-gray-800 text-white text-xs px-2.5 py-1.5 rounded-lg shadow-lg flex items-center gap-1.5">
            <x-ri-shopping-bag-4-line class="size-3" />
            <span>{{ $cartCount }} {{ Str::plural('item', $cartCount) }} in cart</span>
        </div>
        {{-- Tooltip arrow --}}
        <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 dark:bg-gray-800 rotate-45"></div>
    </div>
</div>
@else
<div class="relative group">
    <x-navigation.link 
        :href="route('cart')" 
        class="relative size-10 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300"
    >
        <x-ri-shopping-bag-4-line class="size-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors duration-300" />
    </x-navigation.link>
    
    {{-- Empty cart tooltip --}}
    <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none whitespace-nowrap transform translate-y-2 group-hover:translate-y-0">
        <div class="bg-gray-900 dark:bg-gray-800 text-white text-xs px-2.5 py-1.5 rounded-lg shadow-lg flex items-center gap-1.5">
            <x-ri-shopping-bag-4-line class="size-3" />
            <span>Your cart is empty</span>
        </div>
        <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 dark:bg-gray-800 rotate-45"></div>
    </div>
</div>
@endif