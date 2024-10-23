<div>
    @if($cartCount > 0)
        <x-navigation.link :href="route('cart')">
            Cart
        </x-navigation.link>
    @endif
</div>