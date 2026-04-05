<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-20 md:mt-24 mb-16 animate-in fade-in slide-in-from-bottom-4 duration-700">
    
    {{-- High-Priority Alert: Outstanding Invoice --}}
    @if($invoice = $service->invoices()->where('status', 'pending')->first())
    <div class="w-full mb-8 group">
        <div class="relative overflow-hidden bg-amber-500/10 dark:bg-amber-500/5 border border-amber-500/30 dark:border-amber-500/20 p-6 rounded-2xl shadow-lg transition-all hover:shadow-xl">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="size-12 rounded-2xl bg-amber-500/20 text-amber-500 flex items-center justify-center animate-pulse">
                        <x-ri-error-warning-fill class="size-7" />
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-amber-500">Action Required</h4>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mt-0.5">{{ __('services.outstanding_invoice') }}</p>
                    </div>
                </div>
                <a href="{{ route('invoices.show', $invoice)}}" 
                   class="w-full sm:w-auto px-8 py-3 bg-amber-500 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-amber-600 transition-all hover:scale-105 active:scale-95 shadow-lg shadow-amber-500/20 text-center">
                    {{ __('services.view_and_pay') }}
                </a>
            </div>
            <div class="absolute bottom-0 left-0 h-0.5 w-full bg-amber-500/20">
                <div class="h-full bg-amber-500 w-1/3 animate-pulse"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Service Card --}}
    <div class="bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 rounded-3xl shadow-xl overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-gray-50 to-white dark:from-gray-800/50 dark:to-gray-900 border-b border-gray-200 dark:border-gray-800 p-6 md:p-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="size-14 rounded-2xl bg-primary-100 dark:bg-primary-950/50 border border-primary-200 dark:border-primary-800 flex items-center justify-center text-primary-600 dark:text-primary-400 shadow-lg">
                    <x-ri-instance-line class="size-8" />
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black uppercase tracking-tighter text-gray-900 dark:text-white">{{ $service->product->name }}</h1>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] mt-1">{{ __('services.services') }} ID: #{{ $service->id }}</p>
                </div>
            </div>
            
            <div class="px-5 py-2 rounded-full border text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2
                @if ($service->status == 'active') border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400
                @elseif($service->status == 'suspended') border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400
                @else border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 @endif">
                <span class="relative flex size-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-current"></span>
                    <span class="relative inline-flex rounded-full size-2 bg-current"></span>
                </span>
                <span>
                    {{ $service->cancellation && $service->status == 'active' ? __('services.statuses.cancellation_pending') : __('services.statuses.' . $service->status) }}
                </span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-0">
            {{-- Left Column: Details --}}
            <div class="p-6 md:p-8 border-r border-gray-200 dark:border-gray-800">
                <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 dark:text-gray-400 mb-6">{{ __('services.product_details') }}</h4>
                <div class="space-y-5">
                    @php 
                        $details = [
                            ['label' => __('services.name'), 'value' => $service->product->name, 'primary' => false],
                            ['label' => __('services.price'), 'value' => $service->formattedPrice, 'primary' => true],
                        ];
                        if($service->plan->type == 'recurring') {
                            $details[] = ['label' => __('services.billing_cycle'), 'value' => __('services.every_period', ['period' => $service->plan->billing_period > 1 ? $service->plan->billing_period : '', 'unit' => trans_choice(__('services.billing_cycles.' . $service->plan->billing_unit), $service->plan->billing_period)]), 'primary' => false];
                            $details[] = ['label' => __('services.expires_at'), 'value' => $service->expires_at ? $service->expires_at->format('M d, Y') : 'N/A', 'primary' => false];
                        }
                    @endphp

                    @foreach($details as $detail)
                        <div class="flex justify-between items-center group">
                            <span class="text-[11px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">{{ $detail['label'] }}</span>
                            <span class="text-sm font-black {{ $detail['primary'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-900 dark:text-white' }}">{{ $detail['value'] }}</span>
                        </div>
                    @endforeach

                    <div class="h-px bg-gray-200 dark:bg-gray-800 my-6"></div>

                    @foreach ($fields as $field)
                        <div class="flex justify-between items-center group">
                            <span class="text-[11px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">{{ $field['label'] }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-primary-600 dark:text-primary-400">{{ $field['text'] }}</span>
                                <button onclick="navigator.clipboard.writeText('{{ $field['text'] }}')" class="p-1 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                    <x-ri-file-copy-line class="size-3.5 text-gray-500 dark:text-gray-500 hover:text-primary-600" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right Column: Actions --}}
            <div class="p-6 md:p-8 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col justify-center">
                @if($service->upgradable || count($buttons) > 0 || $service->cancellable)
                <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 dark:text-gray-400 mb-6">{{ __('services.actions') }}</h4>
                <div class="flex flex-col gap-4">
                    @if($service->upgradable)
                        <a href="{{ route('services.upgrade', $service->id) }}" class="w-full">
                            <x-button.primary class="w-full rounded-xl py-3.5 shadow-lg shadow-primary-500/20">
                                <x-ri-arrow-up-circle-line class="size-5 mr-2" />
                                <span>{{ __('services.upgrade') }}</span>
                            </x-button.primary>
                        </a>
                    @endif

                    @foreach ($buttons as $button)
                        @php $btnClass = "w-full rounded-xl py-3.5 shadow-md"; @endphp
                        @if (isset($button['function']))
                            <x-button.primary class="{{ $btnClass }}" wire:click="goto('{{ $button['function'] }}')">
                                {{ $button['label'] }}
                            </x-button.primary>
                        @else
                            <a href="{{ $button['url'] }}" @if(!empty($button['target'])) target="{{ $button['target'] }}" @endif class="w-full">
                                <x-button.primary class="{{ $btnClass }}">
                                    {{ $button['label'] }}
                                </x-button.primary>
                            </a>
                        @endif
                    @endforeach

                    @if($service->cancellable)
                        <button class="w-full rounded-xl py-3.5 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 font-black uppercase text-[10px] tracking-widest hover:bg-red-50 dark:hover:bg-red-950/30 hover:text-red-700 dark:hover:text-red-300 transition-all flex items-center justify-center gap-2 mt-2" 
                                wire:click="$set('showCancel', true)">
                            <x-ri-close-circle-line class="size-4" />
                            <span>{{ __('services.cancel') }}</span>
                        </button>
                    @endif
                </div>
                @else
                    <div class="text-center py-8">
                        <x-ri-shield-check-line class="size-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500">No additional actions available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabs Section --}}
    @if (count($views) > 0)
    <div class="mt-8 bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 rounded-3xl overflow-hidden shadow-xl">
        @if (count($views) > 1)
        <div class="flex bg-gray-50 dark:bg-gray-800/30 border-b border-gray-200 dark:border-gray-800 px-4 overflow-x-auto no-scrollbar">
            @foreach ($views as $view)
            <button wire:click="changeView('{{ $view['name'] }}')"
                class="px-6 md:px-8 py-4 text-[10px] font-black uppercase tracking-[0.3em] transition-all relative group whitespace-nowrap
                {{ $view['name'] == $currentView ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                {{ $view['label'] }}
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-500 transition-all duration-300 {{ $view['name'] == $currentView ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }}"></div>
            </button>
            @endforeach
        </div>
        @endif

        <div class="p-6 md:p-8 relative">
            <div wire:loading wire:target="changeView" class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm z-10 flex items-center justify-center rounded-b-3xl">
                <x-loading />
            </div>
            <div class="animate-in fade-in duration-700">
                {!! $extensionView !!}
            </div>
        </div>
    </div>
    @endif

    {{-- Cancellation Modal --}}
    @if($showCancel)
        <x-modal open="true" title="{{ __('services.cancellation', ['service' => $service->product->name]) }}" width="max-w-xl">
            <div class="p-4">
                <livewire:services.cancel :service="$service" />
            </div>
            <x-slot name="closeTrigger">
                <button wire:click="$set('showCancel', false)" class="size-10 flex items-center justify-center rounded-2xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-ri-close-fill class="size-5 text-gray-500 dark:text-gray-400" />
                </button>
            </x-slot>
        </x-modal>
    @endif
</div>