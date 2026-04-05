<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-20 md:mt-24 mb-16 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="flex flex-col lg:grid lg:grid-cols-4 gap-6 lg:gap-8">
        
        {{-- Main Content - Product Details & Config Options --}}
        <div class="flex flex-col gap-6 w-full lg:col-span-3">
            
            {{-- Product Header Card --}}
            <div class="bg-white/50 dark:bg-gray-900/50 backdrop-blur-xl border border-gray-200 dark:border-gray-800 p-6 md:p-8 rounded-3xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex flex-col md:flex-row gap-6 md:gap-8">
                    @if ($product->image)
                        <div class="shrink-0">
                            <div class="relative group">
                                <div class="absolute -inset-2 bg-primary-500/20 blur-xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" 
                                     class="w-32 h-32 md:w-40 md:h-40 object-cover rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-2xl group-hover:scale-105 transition-transform duration-300">
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex-grow">
                        <div class="flex items-center gap-2 mb-3 flex-wrap">
                            @if ($product->stock === 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-[9px] font-black uppercase tracking-wider text-red-500">
                                    Out of Stock
                                </span>
                            @elseif($product->stock > 0 && $product->stock < 10)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-[9px] font-black uppercase tracking-wider text-orange-500">
                                    Only {{ $product->stock }} Left
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[9px] font-black uppercase tracking-wider text-emerald-500">
                                    In Stock
                                </span>
                            @endif
                        </div>
                        
                        <h1 class="text-2xl md:text-3xl lg:text-4xl font-black uppercase tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-4">
                            {{ $product->name }}
                        </h1>
                        
                        @if($product->description)
                            <div class="max-h-32 overflow-y-auto pr-2 custom-scrollbar">
                                <article class="prose prose-sm dark:prose-invert text-gray-600 dark:text-gray-400 leading-relaxed">
                                    {!! $product->description !!}
                                </article>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Billing Plan Selection --}}
            @if ($product->availablePlans()->count() > 1)
                <div class="space-y-3">
                    <label class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-primary-600 dark:text-primary-400 ml-2">
                        <x-ri-calendar-line class="size-3" />
                        Billing Cycle
                    </label>
                    <x-form.select wire:model.live="plan_id" name="plan_id" 
                        class="!bg-white/50 dark:!bg-gray-900/50 !backdrop-blur-md !border-gray-200 dark:!border-gray-800 !rounded-xl !py-3.5 !px-5 !text-sm font-medium focus:!ring-2 focus:!ring-primary-500 transition-all duration-200">
                        @foreach ($product->availablePlans() as $availablePlan)
                            <option value="{{ $availablePlan->id }}" class="py-2">
                                {{ $availablePlan->name }} — {{ $availablePlan->price()->formatted->price }}
                                @if ($availablePlan->price()->has_setup_fee)
                                    (+ {{ $availablePlan->price()->formatted->setup_fee }} {{ __('product.setup_fee') }})
                                @endif
                            </option>
                        @endforeach
                    </x-form.select>
                </div>
            @endif

            {{-- Configuration Options --}}
            @if($product->configOptions->count() > 0)
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <x-ri-settings-4-line class="size-4 text-primary-600 dark:text-primary-400" />
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Configuration Options
                        </h2>
                    </div>
                    
                    <div class="grid gap-5">
                        @foreach ($product->configOptions as $configOption)
                            @php
                                $showPriceTag = $configOption->children->filter(fn ($value) => !$value->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->is_free)->count() > 0;
                            @endphp
                            
                            <div class="bg-white/30 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-800 p-5 md:p-6 rounded-2xl transition-all duration-300 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-md">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white mb-4">
                                    <span class="w-1.5 h-1.5 bg-primary-500 rounded-full"></span>
                                    {{ $configOption->name }}
                                    @if($configOption->required)
                                        <span class="text-[10px] text-red-500">*Required</span>
                                    @endif
                                </label>
                                
                                <x-form.configoption :config="$configOption" :name="'configOptions.' . $configOption->id" :showPriceTag="$showPriceTag" :plan="$plan">
                                    @if ($configOption->type == 'select')
                                        <div class="relative">
                                            <select name="{{ $configOption->id }}" 
                                                    wire:model.live="configOptions.{{ $configOption->id }}"
                                                    class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200">
                                                <option value="">{{ __('config.select_option') }}</option>
                                                @foreach ($configOption->children as $configOptionValue)
                                                    <option value="{{ $configOptionValue->id }}">
                                                        {{ $configOptionValue->name }}
                                                        {{ ($showPriceTag && $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available) ? ' — ' . $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->formatted->price : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-ri-arrow-down-s-line class="absolute right-4 top-1/2 -translate-y-1/2 size-4 text-gray-400 pointer-events-none" />
                                        </div>
                                    @elseif($configOption->type == 'radio')
                                        <div class="grid sm:grid-cols-2 gap-3 mt-2">
                                            @foreach ($configOption->children as $configOptionValue)
                                                <label class="relative flex items-start p-4 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 cursor-pointer transition-all duration-200 hover:border-primary-400 hover:shadow-md has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/10 dark:has-[:checked]:bg-primary-950/20">
                                                    <input type="radio" 
                                                        name="{{ $configOption->id }}"
                                                        wire:model.live="configOptions.{{ $configOption->id }}"
                                                        value="{{ $configOptionValue->id }}"
                                                        class="peer sr-only" />
                                                    
                                                    <div class="flex items-start gap-3 w-full">
                                                        <div class="size-5 rounded-full border-2 border-gray-300 peer-checked:border-primary-500 peer-checked:bg-primary-500 transition-all flex items-center justify-center flex-shrink-0 mt-0.5">
                                                            <div class="size-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                                        </div>
                                                        
                                                        <div class="flex-1">
                                                            <span class="text-sm font-semibold text-gray-900 dark:text-white block">
                                                                {{ $configOptionValue->name }}
                                                            </span>
                                                            @if($showPriceTag && $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->available)
                                                                <span class="text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase mt-1 block">
                                                                    + {{ $configOptionValue->price(billing_period: $plan->billing_period, billing_unit: $plan->billing_unit)->formatted->price }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </x-form.configoption>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Order Summary Sidebar --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 flex flex-col gap-5 bg-white/60 dark:bg-gray-900/60 backdrop-blur-2xl border border-primary-500/20 dark:border-primary-500/20 rounded-3xl p-6 shadow-2xl">
                
                {{-- Header --}}
                <div class="flex items-center gap-3 border-b border-gray-200 dark:border-gray-800 pb-4">
                    <div class="p-2 bg-primary-500/10 rounded-xl">
                        <x-ri-receipt-line class="size-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <h2 class="text-base font-black uppercase tracking-tighter text-gray-900 dark:text-white">
                        {{ __('product.order_summary') }}
                    </h2>
                </div>

                {{-- Price Breakdown --}}
                <div class="space-y-3">
                    @php
                        $subtotal = $total->subtotal ?? 0;
                        $totalTax = $total->total_tax ?? 0;
                        $setupFee = $total->setup_fee ?? 0;
                    @endphp
                    
                    @if($totalTax > 0)
                        <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <span>{{ __('invoices.subtotal') }}</span>
                            <span>{{ $total->formatted->subtotal ?? $total->format($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <span>{{ \App\Classes\Settings::tax()->name ?? 'Tax' }} ({{ \App\Classes\Settings::tax()->rate ?? 0 }}%)</span>
                            <span>{{ $total->formatted->total_tax ?? $total->format($totalTax) }}</span>
                        </div>
                        @if($setupFee > 0)
                            <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <span>Setup Fee</span>
                                <span>{{ $total->format($setupFee) }}</span>
                            </div>
                        @endif
                        <div class="h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-700 to-transparent my-2"></div>
                    @endif

                    {{-- Total Today --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-[9px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-[0.2em] flex items-center gap-1">
                            <x-ri-calculator-line class="size-3" />
                            {{ __('product.total_today') }}
                        </span>
                        <div class="text-3xl md:text-4xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            {{ $total->formatted->total ?? $total }}
                        </div>
                    </div>

                    {{-- Recurring Info --}}
                    @if (isset($setupFee) && $setupFee > 0 && $plan->type == 'recurring')
                        <div class="p-4 bg-gray-100/50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 mt-3">
                            <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase leading-relaxed flex items-center gap-1">
                                <x-ri-repeat-line class="size-3" />
                                {{ __('product.then_after_x', ['time' => $plan->billing_period . ' ' . trans_choice(__('services.billing_cycles.' . $plan->billing_unit), $plan->billing_period)]) }}:
                            </p>
                            <span class="text-base font-black text-gray-900 dark:text-white">{{ $total->format($total->price ?? 0) }}</span>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">/ {{ $plan->billing_unit }}</span>
                        </div>
                    @endif
                </div>

                {{-- Checkout Button --}}
                @if (($product->stock > 0 || !$product->stock) && $product->price()->available)
                    <div class="mt-3">
                        <x-button.primary wire:click="checkout" wire:loading.attr="disabled" class="w-full py-4 rounded-xl text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300 group">
                            <x-loading target="checkout" />
                            <div wire:loading.remove wire:target="checkout" class="flex items-center justify-center gap-2">
                                <x-ri-lock-line class="size-4" />
                                <span>{{ __('product.checkout') }}</span>
                                <x-ri-arrow-right-line class="size-4 group-hover:translate-x-1 transition-transform duration-200" />
                            </div>
                        </x-button.primary>
                    </div>
                @else
                    <div class="mt-3">
                        <button disabled class="w-full py-4 rounded-xl text-sm font-bold bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                            {{ __('product.unavailable') ?? 'Currently Unavailable' }}
                        </button>
                    </div>
                @endif

                {{-- Security Badges --}}
                <div class="flex flex-col items-center gap-3 mt-3 pt-3 border-t border-gray-200 dark:border-gray-800">
                    <div class="flex items-center justify-center gap-3 opacity-60 group-hover:opacity-100 transition-opacity">
                        <x-ri-shield-check-line class="size-3 text-emerald-500" />
                        <span class="text-[8px] font-black uppercase tracking-[0.3em] text-gray-500 dark:text-gray-400">256-bit SSL Secure</span>
                    </div>
                    <div class="flex items-center justify-center gap-3">
                        <x-ri-secure-payment-line class="size-3 text-primary-500" />
                        <span class="text-[8px] font-black uppercase tracking-[0.3em] text-gray-500 dark:text-gray-400">PCI Compliant</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar Styles */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.3);
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(99, 102, 241, 0.5);
    }
    
    /* Smooth transitions */
    .transition-smooth {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>