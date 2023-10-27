<div>
    @if ($cartItems > 0)
        <a href="{{ route('checkout.index') }}" class="button button-secondary-outline !font-normal">
            <i class="ri-shopping-bag-line"></i>
            {{ $cartItems }}
        </a>
    @endif
</div>
