<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-20 md:mt-24 mb-16 animate-in fade-in duration-700">
    <x-navigation.breadcrumb />

    <div class="mt-6 md:mt-8">
        
        {{-- Setup Payment Method Modal --}}
        @if($setupModalVisible)
        <x-modal :title="__('account.payment_methods')" open="true" width="max-w-lg">
            <x-slot name="closeTrigger">
                <button wire:click="$set('setupModalVisible', false)" class="group p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                    <x-ri-close-fill class="size-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-200" />
                </button>
            </x-slot>
            
            <div class="space-y-6 py-4">
                <div class="flex items-center gap-3 pb-3 border-b border-gray-200 dark:border-gray-800">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-950/50 rounded-xl flex items-center justify-center">
                        <x-ri-bank-card-line class="size-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('account.add_payment_method') }}</h3>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400">Choose a payment gateway to add a new method</p>
                    </div>
                </div>
                
                @if(count($this->gateways) > 1)
                <x-form.select name="gateway" :label="__('account.input.payment_gateway')" wire:model.live="gateway" class="!bg-gray-50 dark:!bg-gray-800/50 !rounded-xl" required>
                    @foreach($this->gateways as $gateway)
                    <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                    @endforeach
                </x-form.select>
                @elseif(count($this->gateways) === 1)
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Gateway: <span class="font-bold">{{ $this->gateways->first()->name }}</span>
                    </p>
                </div>
                @elseif(count($this->gateways) === 0)
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-center">
                    <p class="text-xs font-black uppercase tracking-widest text-red-600 dark:text-red-400">{{ __('account.no_payment_gateways_available') }}</p>
                </div>
                @endif

                <x-button.primary class="w-full !py-3.5 text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-primary-500/20 hover:scale-105 active:scale-95 transition-all duration-200" wire:click="createBillingAgreement" wire:loading.attr="disabled">
                    <x-loading target="createBillingAgreement" />
                    <div wire:loading.remove wire:target="createBillingAgreement" class="flex items-center justify-center gap-2">
                        <x-ri-add-line class="size-4" />
                        {{ __('account.setup_payment_method') }}
                    </div>
                </x-button.primary>
            </div>

            @if ($this->setup)
            <x-modal :title="__('account.setup_payment_method')" open width="max-w-2xl">
                <div class="mt-6 p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-200 dark:border-gray-700">
                    {{ $this->setup }}
                </div>
                <x-slot name="closeTrigger">
                    <button wire:confirm="Are you sure?" wire:click="cancelSetup" wire:loading.attr="disabled" class="group p-2 rounded-full hover:bg-red-100 dark:hover:bg-red-950/30 transition-all">
                        <x-ri-close-fill class="size-5 text-gray-500 dark:text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400" />
                    </button>
                </x-slot>
            </x-modal>
            @endif
        </x-modal>
        @endif

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-6 animate-in slide-in-from-top-4 duration-1000">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-px bg-gradient-to-r from-primary-500 to-transparent"></div>
                    <p class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-[0.3em]">Payments</p>
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                    {{ __('account.saved_payment_methods') }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('account.saved_payment_methods_description') }}</p>
                <div class="w-12 h-0.5 bg-primary-500 rounded-full mt-4"></div>
            </div>
            
            @if(count($this->gateways) > 0)
            <x-button.primary class="w-full md:w-auto !py-3 !px-6 text-[10px] font-black uppercase tracking-[0.2em] shadow-lg hover:scale-105 active:scale-95 transition-all duration-200" wire:click="$set('setupModalVisible', true)">
                <x-ri-add-line class="size-4 mr-2" />
                {{ __('account.add_payment_method') }}
            </x-button.primary>
            @endif
        </div>

        {{-- Payment Methods Section --}}
        @php $groupedAgreements = $billingAgreements->groupBy('gateway.name'); @endphp

        @if($groupedAgreements->count() > 0)
            @foreach($groupedAgreements as $gatewayName => $agreements)
            <div class="mb-10 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 flex items-center justify-center p-2">
                        @if($agreements->first()?->gateway?->meta?->icon)
                        <img src="{{ $agreements->first()->gateway->meta->icon }}" alt="{{ $gatewayName }}" class="w-full h-full object-contain" />
                        @else
                        <x-ri-secure-payment-line class="size-5 text-primary-500" />
                        @endif
                    </div>
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-gray-700 dark:text-gray-300">{{ $gatewayName }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full bg-primary-100 dark:bg-primary-950/50 text-primary-700 dark:text-primary-400 text-[10px] font-black">{{ $agreements->count() }}</span>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($agreements as $agreement)
                    <div class="group relative bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 p-5 rounded-2xl shadow-md hover:shadow-xl hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-300">
                        <div class="flex flex-col gap-4">
                            <div class="flex justify-between items-start">
                                <div class="w-12 h-8 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                    @switch(strtolower($agreement->type))
                                        @case('visa')
                                            <x-icons.visa class="h-5" />
                                            @break
                                        @case('mastercard')
                                            <x-icons.mastercard class="h-5" />
                                            @break
                                        @case('paypal')
                                            <x-icons.paypal class="h-5" />
                                            @break
                                        @case('amex')
                                            <x-icons.amex class="h-5" />
                                            @break
                                        @default
                                            <x-ri-bank-card-line class="size-6 text-primary-500" />
                                    @endswitch
                                </div>
                                <button class="opacity-0 group-hover:opacity-100 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/30 hover:text-red-600 dark:hover:text-red-400 transition-all duration-200"
                                    x-on:click.prevent="$store.confirmation.confirm({
                                        title: '{{ __('account.remove_payment_method') }}',
                                        message: '{{ __('account.remove_payment_method_confirm', ['name' => $agreement->name]) }}',
                                        callback: () => $wire.removePaymentMethod('{{ $agreement->ulid }}')
                                    })">
                                    <x-ri-delete-bin-line class="size-4" />
                                </button>
                            </div>
                            
                            <div>
                                <div class="text-sm font-black text-gray-900 dark:text-white truncate">{{ $agreement->name }}</div>
                                @if($agreement->expiry)
                                <div class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mt-1">
                                    Expires: {{ \Carbon\Carbon::parse($agreement->expiry)->format('m/Y') }}
                                </div>
                                @endif
                                @if($agreement->is_default)
                                <div class="mt-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[8px] font-black bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 rounded-full">
                                        <x-ri-checkbox-circle-line class="size-2" />
                                        Default
                                    </span>
                                </div>
                                @endif
                            </div>

                            @if($agreement->services()->count() > 0)
                            <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                                <span class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1">
                                    <x-ri-service-line class="size-3" />
                                    {{ $agreement->services()->count() }} {{ __('account.active_subscriptions') }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        @else
        <div class="bg-gray-50/50 dark:bg-gray-900/30 backdrop-blur-sm border-2 border-dashed border-gray-200 dark:border-gray-800 p-12 rounded-2xl text-center mb-10 animate-in fade-in duration-500">
            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <x-ri-bank-card-line class="size-10 text-gray-400 dark:text-gray-600" />
            </div>
            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">{{ __('account.no_saved_payment_methods') }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Add your first payment method to get started</p>
        </div>
        @endif

        {{-- Recent Transactions Section --}}
        @if($transactions->count() > 0)
        <div class="mt-16 animate-in fade-in duration-1000">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-950/50 rounded-xl flex items-center justify-center">
                    <x-ri-history-line class="size-5 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-tighter text-gray-900 dark:text-white">{{ __('account.recent_transactions') }}</h3>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">Your latest payment activity</p>
                </div>
                <div class="h-px flex-1 bg-gradient-to-r from-gray-200 dark:from-gray-800 to-transparent"></div>
            </div>

            <div class="space-y-3">
                @foreach ($transactions as $transaction)
                <a href="{{ route('invoices.show', $transaction->invoice) }}" wire:navigate class="block group">
                    <div class="bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 p-4 rounded-xl hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-md transition-all duration-300">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-primary-500 group-hover:scale-110 transition-transform duration-300">
                                    <x-ri-bill-line class="size-5" />
                                </div>
                                <div>
                                    <div class="text-sm font-black text-gray-900 dark:text-white">
                                        {{ $transaction->transaction_id ?? 'Transaction #' . $transaction->id }}
                                    </div>
                                    <div class="text-[10px] font-medium text-gray-500 dark:text-gray-400">
                                        {{ $transaction->created_at->format('d M Y • H:i') }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center justify-between sm:justify-end gap-3">
                                <div class="text-left sm:text-right">
                                    <div class="text-base font-black text-gray-900 dark:text-white">{{ $transaction->formattedAmount }}</div>
                                    <div class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $transaction->gateway->name ?? 'System' }}</div>
                                </div>

                                <div>
                                    @if($transaction->status === \App\Enums\InvoiceTransactionStatus::Succeeded)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        <x-ri-check-line class="size-3 mr-1" /> Succeeded
                                    </span>
                                    @elseif($transaction->status === \App\Enums\InvoiceTransactionStatus::Processing)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-yellow-50 dark:bg-yellow-950/30 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800">
                                        <x-ri-loader-5-fill class="size-3 mr-1 animate-spin" /> Processing
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800">
                                        <x-ri-close-line class="size-3 mr-1" /> Failed
                                    </span>
                                    @endif
                                </div>
                                
                                <x-ri-arrow-right-s-line class="size-5 text-gray-400 group-hover:text-primary-500 transition-colors" />
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        </div>
        @endif
    </div>
</div>