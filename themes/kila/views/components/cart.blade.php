@if($cartCount > 0)
<div class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-neutral transition">
    <x-navigation.link :href="route('cart')">
        <x-ri-shopping-bag-4-fill class="size-4" />
    </x-navigation.link>
</div>
@endif