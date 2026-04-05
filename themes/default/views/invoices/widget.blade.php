<div class="space-y-4 animate-in fade-in slide-in-from-bottom-3 duration-500">
    @foreach ($invoices as $invoice)
    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="group block">
        <div class="relative overflow-hidden bg-white/[0.03] backdrop-blur-md border border-white/5 p-5 rounded-2xl transition-all duration-300 group-hover:bg-white/[0.07] group-hover:border-primary/30 group-hover:-translate-y-0.5 shadow-lg">
            
            <div class="absolute left-0 top-0 h-full w-1 
                @if ($invoice->status == 'paid') bg-success/40 
                @elseif($invoice->status == 'cancelled') bg-neutral/30
                @else bg-warning/40 shadow-[0_0_10px_rgba(245,158,11,0.3)] 
                @endif">
            </div>

            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <div class="bg-primary/10 p-2.5 rounded-xl border border-primary/20 text-primary group-hover:scale-110 transition-transform">
                        <x-ri-bill-line class="size-5" />
                    </div>
                    
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-base group-hover:text-primary transition-colors">
                            {{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice', ['id' => $invoice->id]) : __('invoices.invoice', ['id' => $invoice->number]) }}
                        </h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-lg font-black tracking-tighter text-base">{{ $invoice->formattedTotal }}</span>
                            <span class="text-[8px] font-bold text-base/20 uppercase tracking-[0.2em] border border-white/5 px-1.5 py-0.5 rounded">Credit-Auth</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 px-3 py-1 rounded-lg border text-[9px] font-black uppercase tracking-widest
                    @if ($invoice->status == 'paid') border-success/20 bg-success/5 text-success
                    @elseif($invoice->status == 'cancelled') border-white/5 bg-white/5 text-base/30
                    @else border-warning/20 bg-warning/5 text-warning @endif">
                    
                    @if ($invoice->status == 'paid')
                        <x-ri-checkbox-circle-fill class="size-3.5" />
                    @elseif($invoice->status == 'cancelled')
                        <x-ri-forbid-fill class="size-3.5" />
                    @else
                        <x-ri-error-warning-fill class="size-3.5 animate-pulse" />
                    @endif
                    
                    <span>{{ $invoice->status }}</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-white/5 space-y-2">
                @foreach ($invoice->items as $item)
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 size-1 rounded-full bg-primary/40"></div>
                    <p class="text-[10px] font-bold text-base/50 leading-relaxed tracking-tight flex-1">
                        <span class="text-base/80 uppercase tracking-widest">Item:</span> 
                        {{ $item->description }}
                    </p>
                    <p class="text-[9px] font-black text-base/30 uppercase italic whitespace-nowrap">
                        {{ $invoice->created_at->format('d M Y') }}
                    </p>
                </div>
                @endforeach
            </div>

            <div class="absolute bottom-1 right-3 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                <span class="text-[8px] font-mono text-primary/40 uppercase">View_Manifest_Details //</span>
            </div>
        </div>
    </a>
    @endforeach
</div>