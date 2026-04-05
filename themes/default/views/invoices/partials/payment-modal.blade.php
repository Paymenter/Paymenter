<x-modal :title="config('settings.invoice_proforma', false) ? __('invoices.payment_for_proforma_invoice', ['id' => $invoice->id]) : __('invoices.payment_for_invoice', ['number' => $invoice->number])" open width="max-w-xl">
    <x-slot name="closeTrigger">
        <div class="flex items-center gap-6">
            <div class="flex flex-col items-end">
                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-base/30 leading-none mb-1">
                    Outstanding_Balance
                </span>
                <span class="text-lg font-black tracking-tighter text-primary drop-shadow-[0_0_10px_rgba(var(--primary-rgb),0.3)]">
                    {{ $invoice->formattedRemaining }}
                </span>
            </div>

            <button wire:confirm="Are you sure?" wire:click="exitPay" @click="open = false" 
                class="size-10 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 hover:bg-error/10 hover:border-error/30 hover:text-error transition-all group">
                <x-ri-close-line class="size-6 group-hover:rotate-90 transition-transform duration-300" />
            </button>
        </div>
    </x-slot>

    <div class="relative">
        <div class="flex items-center gap-2 mb-8 px-1">
            <div class="size-2 rounded-full bg-success animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-base/40">Secure_Gateway_Active</span>
            <div class="h-px flex-1 bg-gradient-to-r from-white/10 to-transparent ml-2"></div>
        </div>

        @if ($this->pay)
            <div class="mt-8 p-6 bg-white/[0.02] border border-white/5 rounded-2xl shadow-inner animate-in fade-in zoom-in-95 duration-300">
                <div class="flex items-center gap-4 mb-6">
                    <x-ri-shield-check-line class="size-8 text-primary/60" />
                    <p class="text-xs font-bold text-base/60 leading-relaxed uppercase tracking-wide">
                        Initialising encrypted transaction stream. Please do not close the terminal.
                    </p>
                </div>
                
                <div class="payment-frame-wrapper">
                    {{ $this->pay }}
                </div>
            </div>
        @else
            <div class="animate-in slide-in-from-bottom-4 duration-500">
                @include('invoices.partials.payment-options')
            </div>
        @endif

        <div class="mt-8 flex justify-between items-center px-1">

            <div class="flex gap-1">
                <div class="w-8 h-1 bg-primary/20 rounded-full"></div>
                <div class="w-4 h-1 bg-primary/10 rounded-full"></div>
                <div class="w-2 h-1 bg-primary/5 rounded-full"></div>
            </div>
        </div>
    </div>
</x-modal>