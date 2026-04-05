<div class="container mt-14 animate-in fade-in zoom-in-95 duration-500">
    <div @if ($checkPayment) wire:poll.5s="checkPaymentStatus" @endif>
        @if ($this->pay || $showPayModal)
            @include('invoices.partials.payment-modal')
        @endif

        <div class="flex justify-end mb-4">
            <button wire:click="downloadPDF" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-base/40 hover:text-primary transition-all">
                <span wire:loading wire:target="downloadPDF">
                    <x-ri-loader-5-fill class="size-4 animate-spin text-primary" />
                </span>
                <span wire:loading.remove wire:target="downloadPDF" class="flex items-center gap-2">
                    <x-ri-download-cloud-2-line class="size-4 group-hover:translate-y-0.5 transition-transform" />
                    {{ __('invoices.download_pdf') }}
                </span>
            </button>
        </div>

        <div class="bg-white/[0.02] backdrop-blur-xl border border-white/5 p-8 md:p-12 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03] pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-start gap-8">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black uppercase tracking-tighter text-base">
                            {{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice', ['id' => $invoice->id]) : __('invoices.invoice', ['id' => $invoice->number]) }}
                        </h1>
                        <div class="flex items-center gap-4 mt-3">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black uppercase tracking-widest text-base/30">{{ __('invoices.invoice_date') }}</span>
                                <span class="text-xs font-bold text-base/80">{{ $invoice->created_at->format('d M Y') }}</span>
                            </div>
                            @if($invoice->due_at)
                            <div class="w-px h-6 bg-white/10"></div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black uppercase tracking-widest text-base/30">{{ __('invoices.due_date') }}</span>
                                <span class="text-xs font-bold @if($invoice->status != 'paid' && $invoice->due_at->isPast()) text-error @else text-base/80 @endif">
                                    {{ $invoice->due_at->format('d M Y') }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="w-full md:w-72">
                        @if ($invoice->status == 'paid')
                            <div class="bg-success/10 border border-success/20 rounded-2xl p-4 text-center">
                                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-success block mb-1">Authorization Verified</span>
                                <span class="text-xl font-black uppercase text-success flex items-center justify-center gap-2">
                                    <x-ri-checkbox-circle-fill class="size-6" />
                                    {{ __('invoices.paid') }}
                                </span>
                            </div>
                        @else
                            <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
                                @if($checkPayment || $invoice->transactions->where('status', \App\Enums\InvoiceTransactionStatus::Processing)->where('created_at', '>=', now()->subDays(1))->count() > 0)
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-warning animate-pulse">{{ __('invoices.payment_processing') }}</span>
                                    <x-ri-loader-5-fill class="size-6 mx-auto mt-2 text-warning animate-spin" />
                                @else
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-base/40 block mb-3">{{ __('invoices.payment_pending') }}</span>
                                    <x-button.primary wire:click="$set('showPayModal', true)" class="w-full !rounded-xl !py-3 !text-[11px] !font-black !uppercase !tracking-widest">
                                        <span wire:loading wire:target="pay">Processing...</span>
                                        <span wire:loading.remove wire:target="pay">Initiate Payment</span>
                                    </x-button.primary>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mt-16 pb-12 border-b border-white/5">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary mb-4 opacity-70">// {{ __('invoices.issued_to') }}</p>
                        <div class="space-y-1">
                            <p class="text-lg font-black text-base">{{ $invoice->user_name }}</p>
                            @foreach($invoice->user_properties as $property)
                                <p class="text-sm font-medium text-base/50">{{ $property }}</p>
                            @endforeach
                        </div>
                    </div>
                    <div class="md:text-right">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary mb-4 opacity-70 md:justify-end flex">// {{ __('invoices.bill_to') }}</p>
                        <div class="text-sm font-medium text-base/50 leading-relaxed">
                            {!! nl2br(e($invoice->bill_to)) !!}
                        </div>
                    </div>
                </div>

                <div class="mt-12">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-base/20 mb-6">Service Manifest</p>
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-2">
                            <thead>
                                <tr class="text-[10px] font-black uppercase tracking-widest text-base/40">
                                    <th class="text-left px-4 pb-4">{{ __('invoices.item') }}</th>
                                    <th class="text-left px-4 pb-4">{{ __('invoices.price') }}</th>
                                    <th class="text-left px-4 pb-4">{{ __('invoices.quantity') }}</th>
                                    <th class="text-right px-4 pb-4">{{ __('invoices.total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="space-y-2">
                                @foreach ($invoice->items as $item)
                                <tr class="group bg-white/[0.02] hover:bg-white/[0.05] transition-colors">
                                    <td class="p-4 rounded-l-2xl border-y border-l border-white/5">
                                        @if(in_array($item->reference_type, ['App\Models\Service', 'App\Models\ServiceUpgrade']))
                                            <a href="{{ route('services.show', $item->reference_type == 'App\Models\Service' ? $item->reference_id : $item->reference->service_id) }}"
                                               class="text-sm font-bold text-base hover:text-primary transition-colors">
                                               {{ $item->description }}
                                            </a>
                                        @else
                                            <span class="text-sm font-bold text-base">{{ $item->description }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 border-y border-white/5 text-sm font-medium text-base/60">{{ $item->formattedPrice }}</td>
                                    <td class="p-4 border-y border-white/5 text-sm font-medium text-base/60">{{ $item->quantity }}</td>
                                    <td class="p-4 rounded-r-2xl border-y border-r border-white/5 text-right font-black text-base">{{ $item->formattedTotal }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-12 flex flex-col items-end">
                    <div class="w-full md:w-80 space-y-3 bg-white/[0.03] p-6 rounded-3xl border border-white/5">
                        @if ($invoice->formattedTotal->tax > 0)
                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-base/40">
                            <span>{{ __('invoices.subtotal') }}</span>
                            <span class="text-base/80">{{ $invoice->formattedTotal->format($invoice->formattedTotal->subtotal) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-base/40">
                            <span>{{ $invoice->tax->name }} ({{ $invoice->tax->rate }}%)</span>
                            <span class="text-base/80">{{ $invoice->formattedTotal->formatted->tax }}</span>
                        </div>
                        <div class="h-px bg-white/5 my-2"></div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-black uppercase tracking-[0.2em] text-primary">Grand Total</span>
                            <span class="text-2xl font-black tracking-tighter text-base">{{ $invoice->formattedTotal }}</span>
                        </div>
                    </div>
                </div>

                @if ($invoice->transactions->isNotEmpty())
                <div class="mt-20">
                    <div class="flex items-center gap-4 mb-6">
                        <h2 class="text-[11px] font-black uppercase tracking-[0.4em] text-base/30">{{ __('invoices.transactions') }}</h2>
                        <div class="h-px flex-1 bg-white/5"></div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-1">
                            <tbody class="text-[10px] font-bold uppercase tracking-tight">
                                @foreach ($invoice->transactions->sortByDesc('created_at') as $transaction)
                                <tr class="text-base/40 hover:text-base/80 transition-colors">
                                    <td class="py-3 px-2 whitespace-nowrap">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td class="py-3 px-2 font-mono tracking-tighter">{{ $transaction->transaction_id }}</td>
                                    <td class="py-3 px-2">{{ $transaction->is_credit_transaction ? __('invoices.paid_with_credits') : $transaction->gateway?->name }}</td>
                                    <td class="py-3 px-2 text-base font-black">{{ $transaction->formattedAmount }}</td>
                                    <td class="py-3 px-2 text-right">
                                        @if($transaction->status == \App\Enums\InvoiceTransactionStatus::Succeeded)
                                            <span class="text-success border border-success/20 px-2 py-0.5 rounded-md bg-success/5">Succeeded</span>
                                        @elseif($transaction->status == \App\Enums\InvoiceTransactionStatus::Processing)
                                            <span class="text-warning border border-warning/20 px-2 py-0.5 rounded-md bg-warning/5 animate-pulse">Processing</span>
                                        @else
                                            <span class="text-error border border-error/20 px-2 py-0.5 rounded-md bg-error/5">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>