<div class="container mt-14 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="px-2">
        <x-navigation.breadcrumb class="text-[10px] uppercase tracking-[0.2em] opacity-40 hover:opacity-100 transition-opacity" />
    </div>

    <div class="space-y-4">
        @forelse ($invoices as $invoice)
        <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="group block">
            <div class="relative overflow-hidden bg-white/5 backdrop-blur-md border border-neutral/20 p-5 rounded-2xl transition-all duration-300 group-hover:bg-white/[0.08] group-hover:border-primary/30 group-hover:-translate-y-0.5 shadow-sm">
                
                <div class="absolute left-0 top-0 h-full w-1 
                    @if ($invoice->status == 'paid') bg-success/50 shadow-[0_0_10px_rgba(34,197,94,0.4)] 
                    @elseif($invoice->status == 'cancelled') bg-neutral/40
                    @else bg-warning/50 shadow-[0_0_10px_rgba(245,158,11,0.4)] 
                    @endif">
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-4">
                        <div class="size-11 flex items-center justify-center rounded-xl bg-primary/10 border border-primary/20 text-primary transition-transform group-hover:rotate-12">
                            <x-ri-bill-line class="size-6" />
                        </div>
                        
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-black uppercase tracking-widest text-base">
                                    {{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice', ['id' => $invoice->id]) : __('invoices.invoice', ['id' => $invoice->number]) }}
                                </h3>
                                @if(!$invoice->number)
                                    <span class="text-[8px] font-black px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-base/40 uppercase tracking-tighter">Draft</span>
                                @endif
                            </div>
                            <p class="text-[10px] font-bold text-base/30 uppercase tracking-[0.2em] mt-0.5">
                                Issued: {{ $invoice->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between md:justify-end gap-6">
                        <div class="text-right">
                            <p class="text-[10px] font-black uppercase tracking-widest text-base/30 leading-none mb-1">Amount Due</p>
                            <span class="text-lg font-black tracking-tighter text-base">{{ $invoice->formattedTotal }}</span>
                        </div>

                        <div class="flex items-center gap-2 px-4 py-1.5 rounded-full border transition-colors
                            @if ($invoice->status == 'paid') border-success/20 bg-success/5 text-success
                            @elseif($invoice->status == 'cancelled') border-white/10 bg-white/5 text-base/40
                            @else border-warning/20 bg-warning/5 text-warning @endif">
                            
                            <span class="text-[9px] font-black uppercase tracking-[0.15em]">
                                {{ $invoice->status }}
                            </span>

                            <div class="size-4">
                                @if ($invoice->status == 'paid')
                                    <x-ri-checkbox-circle-fill class="size-4" />
                                @elseif($invoice->status == 'cancelled')
                                    <x-ri-forbid-fill class="size-4" />
                                @else
                                    <x-ri-error-warning-fill class="size-4 animate-pulse" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-white/5 space-y-1">
                    @foreach ($invoice->items as $item)
                        <div class="flex items-center gap-2 text-[10px] font-bold text-base/50 uppercase tracking-wide">
                            <x-ri-arrow-right-s-line class="size-3 text-primary/40" />
                            <span class="truncate">{{ $item->description }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </a>
        @empty
        <div class="bg-white/5 border border-dashed border-white/10 p-12 rounded-[2rem] text-center">
            <x-ri-inbox-line class="size-12 text-base/10 mx-auto mb-4" />
            <p class="text-xs font-black uppercase tracking-[0.3em] text-base/20">{{ __('invoices.no_invoices') }}</p>
        </div>
        @endforelse
    </div>

    <div class="pt-6 border-t border-white/5">
        {{ $invoices->links() }}
    </div>
</div>