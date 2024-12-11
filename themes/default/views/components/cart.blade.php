<div class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-neutral transition">
    @if($cartCount > 0)
        <x-navigation.link :href="route('cart')">
            <x-ri-shopping-bag-4-fill class="w-4 h-4" />
        </x-navigation.link>
    @endif
</div>