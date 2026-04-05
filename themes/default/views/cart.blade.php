<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-20 md:mt-24 mb-16 animate-in fade-in duration-700">
    <div class="flex flex-col lg:grid lg:grid-cols-4 gap-8 lg:gap-10">
        
        {{-- Cart Items Section --}}
        <div class="flex flex-col col-span-3 gap-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <x-navigation.breadcrumb />
                    <h1 class="text-3xl md:text-4xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mt-2">
                        Shopping Cart
                    </h1>
                </div>
                <span class="text-xs font-black text-primary-600 dark:text-primary-400 uppercase tracking-[0.2em] bg-primary-100 dark:bg-primary-950/50 px-4 py-2 rounded-full">
                    {{ Cart::items()->count() }} {{ __('product.items') }}
                </span>
            </div>

            @forelse (Cart::items() as $index => $item)
                <div class="group relative flex flex-col sm:flex-row justify-between w-full p-6 rounded-2xl bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 transition-all duration-500 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-xl animate-in fade-in slide-in-from-bottom-6"
                     style="animation-delay: {{ $index * 100 }}ms; animation-fill-mode: both;">
                    
                    {{-- Product Image (Optional) --}}
                    @if($item->product->image)
                    <div class="absolute -top-3 -left-3 w-12 h-12 rounded-xl overflow-hidden border-2 border-white dark:border-gray-800 shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                    </div>
                    @endif
                    
                    <div class="flex-1">
                        <h2 class="text-xl md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-300">
                            {{ $item->product->name }}
                        </h2>
                        
                        {{-- Billing Plan --}}
                        <div class="mt-2 mb-3">
                            <span class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-wider bg-primary-50 dark:bg-primary-950/30 px-2 py-0.5 rounded-full">
                                {{ $item->plan->name }}
                            </span>
                        </div>
                        
                        {{-- Configuration Options --}}
                        @if(count($item->config_options) > 0)
                            <div class="space-y-1.5 mt-3">
                                @foreach ($item->config_options as $option)
                                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-2">
                                        <span class="size-1 rounded-full bg-emerald-500"></span>
                                        <span class="text-emerald-600 dark:text-emerald-400">{{ $option['option_name'] }}:</span> 
                                        <span class="text-gray-700 dark:text-gray-300">{{ $option['value_name'] }}</span>
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col justify-between items-end gap-4 mt-6 sm:mt-0">
                        {{-- Price --}}
                        <div class="text-right">
                            <h3 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white tracking-tighter">
                                {{ $item->price->format($item->price->total * $item->quantity) }}
                            </h3>
                            @if ($item->quantity > 1)
                                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.1em]">
                                    {{ $item->price->format($item->price->total) }} {{ __('product.each') }}
                                </p>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap items-center gap-2">
                            {{-- Quantity Selector --}}
                            @if ($item->product->allow_quantity == 'combined')
                            <div class="flex items-center bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-0.5">
                                <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                        class="size-7 flex items-center justify-center hover:bg-primary-100 dark:hover:bg-primary-950/50 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg transition-all duration-300"
                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                    <x-ri-subtract-line class="size-3" />
                                </button>
                                <span class="w-8 text-center font-black text-sm text-gray-900 dark:text-white">{{ $item->quantity }}</span>
                                <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                        class="size-7 flex items-center justify-center hover:bg-primary-100 dark:hover:bg-primary-950/50 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg transition-all duration-300">
                                    <x-ri-add-line class="size-3" />
                                </button>
                            </div>
                            @endif

                            {{-- Edit Button --}}
                            <a href="{{ route('products.checkout', [$item->product->category, $item->product, 'edit' => $item->id]) }}" wire:navigate>
                                <x-button.secondary class="!px-4 !py-2 text-[10px] font-black uppercase tracking-[0.2em] border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-all">
                                    <x-ri-edit-line class="size-3 mr-1" />
                                    {{ __('product.edit') }}
                                </x-button.secondary>
                            </a>

                            {{-- Remove Button --}}
                            <x-button.danger wire:click="removeProduct({{ $item->id }})" 
                                             class="!px-4 !py-2 text-[10px] font-black uppercase tracking-[0.2em] bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-600 hover:text-white transition-all">
                                <x-loading target="removeProduct({{ $item->id }})" />
                                <span wire:loading.remove wire:target="removeProduct({{ $item->id }})">
                                    <x-ri-delete-bin-line class="size-3" />
                                    {{ __('product.remove') }}
                                </span>
                            </x-button.danger>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty Cart State --}}
                <div class="p-16 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/30 flex flex-col items-center justify-center text-center">
                    <div class="w-24 h-24 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-6">
                        <x-ri-shopping-bag-line class="size-10 text-gray-400 dark:text-gray-600" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                        Your Cart is Empty
                    </h2>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Looks like you haven't added anything to your cart yet</p>
                    <a href="{{ route('category.index') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-primary-700 transition-all">
                        <x-ri-shopping-bag-line class="size-4" />
                        Start Shopping
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Order Summary Sidebar --}}
        @if (Cart::items()->count() > 0)
        <div class="flex flex-col gap-6 animate-in fade-in slide-in-from-right-8 duration-1000">
            <div class="sticky top-24 p-6 rounded-2xl bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-primary-200 dark:border-primary-800 shadow-xl">
                
                <div class="flex items-center gap-2 mb-6 pb-3 border-b border-gray-200 dark:border-gray-800">
                    <x-ri-receipt-line class="size-4 text-primary-500" />
                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-gray-500 dark:text-gray-400">
                        Order Summary
                    </h2>
                </div>

                {{-- Coupon Section --}}
                <div class="mb-6">
                    @if(!$coupon)
                    <div class="flex items-end gap-2">
                        <div class="flex-grow">
                            <x-form.input wire:model="coupon_code" name="coupon_code" placeholder="Enter coupon code" class="!rounded-xl !py-2.5 text-sm" />
                        </div>
                        <x-button.primary wire:click="applyCoupon" class="!py-2.5 !px-4 rounded-xl" wire:loading.attr="disabled">
                            <x-loading target="applyCoupon" />
                            <span wire:loading.remove wire:target="applyCoupon" class="text-[10px] font-black uppercase tracking-wider">Apply</span>
                        </x-button.primary>
                    </div>
                    @else
                    <div class="flex justify-between items-center p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl animate-in zoom-in duration-300">
                        <div class="flex items-center gap-2">
                            <x-ri-ticket-2-line class="size-4 text-emerald-600 dark:text-emerald-400" />
                            <span class="font-black text-emerald-600 dark:text-emerald-400 tracking-wider text-xs">{{ $coupon->code }}</span>
                        </div>
                        <button wire:click="removeCoupon" class="text-[10px] font-black text-red-500 hover:text-red-600 uppercase tracking-wider transition-colors">
                            Remove
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Price Breakdown --}}
                <div class="space-y-3 pb-4 mb-4 border-b border-gray-200 dark:border-gray-800">
                    <div class="flex justify-between text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span class="text-gray-900 dark:text-white">{{ $total->format($total->subtotal) }}</span>
                    </div>
                    
                    @if ($total->discount > 0)
                    <div class="flex justify-between text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                        <span>Discount</span>
                        <span>- {{ $total->format($total->discount) }}</span>
                    </div>
                    @endif
                    
                    @if ($total->tax > 0)
                    <div class="flex justify-between text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <span>{{ \App\Classes\Settings::tax()->name ?? 'Tax' }} ({{ \App\Classes\Settings::tax()->rate ?? 0 }}%)</span>
                        <span class="text-gray-900 dark:text-white">{{ $total->format($total->tax) }}</span>
                    </div>
                    @endif

                    @if ($total->setup_fee > 0)
                    <div class="flex justify-between text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <span>Setup Fee</span>
                        <span class="text-gray-900 dark:text-white">{{ $total->format($total->setup_fee) }}</span>
                    </div>
                    @endif
                </div>

                {{-- Total --}}
                <div class="flex justify-between items-end mb-6">
                    <span class="text-xs font-black uppercase tracking-[0.2em] text-primary-600 dark:text-primary-400">Total</span>
                    <span class="text-3xl md:text-4xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                        {{ $total->format($total->total) }}
                    </span>
                </div>

                {{-- Terms & Conditions --}}
                @if(config('settings.tos'))
                <div class="mb-5 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                    <x-form.checkbox wire:model="tos" name="tos" class="!items-start">
                        <div class="text-[10px] font-bold text-gray-600 dark:text-gray-400 leading-relaxed">
                            I agree to the 
                            <a href="{{ config('settings.tos') }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline font-black">
                                Terms of Service
                            </a>
                        </div>
                    </x-form.checkbox>
                </div>
                @endif

                {{-- Checkout Button --}}
                <x-button.primary wire:click="checkout" 
                                 class="w-full !py-4 text-xs font-black uppercase tracking-[0.3em] shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300" 
                                 wire:loading.attr="disabled">
                    <x-loading target="checkout" />
                    <span wire:loading.remove wire:target="checkout" class="flex items-center justify-center gap-2">
                        <x-ri-lock-line class="size-4" />
                        Proceed to Checkout
                    </span>
                </x-button.primary>
                
                {{-- Trust Badges --}}
                <div class="mt-4 pt-3 flex flex-wrap items-center justify-center gap-3 text-[9px] font-black uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500">
                    <span class="flex items-center gap-1">
                        <x-ri-shield-check-line class="size-3 text-green-500" />
                        Secure Payment
                    </span>
                    <span class="w-px h-3 bg-gray-300 dark:bg-gray-700"></span>
                    <span class="flex items-center gap-1">
                        <x-ri-lock-line class="size-3 text-green-500" />
                        256-bit SSL
                    </span>
                    <span class="w-px h-3 bg-gray-300 dark:bg-gray-700"></span>
                    <span class="flex items-center gap-1">
                        <x-ri-customer-service-line class="size-3 text-blue-500" />
                        24/7 Support
                    </span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>