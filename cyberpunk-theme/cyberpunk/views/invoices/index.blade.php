<div class="container mt-10 pb-10 space-y-4">
    <x-navigation.breadcrumb />

    <div class="cyber-card cyber-clip p-5 flex flex-wrap items-center gap-4 justify-between">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-lg bg-primary/10 border border-primary/25">
                <x-ri-bill-fill class="size-5 text-primary" />
            </div>
            <div>
                <h1 class="text-xl font-black">{{ __('navigation.invoices') }}</h1>
                <p class="text-sm text-base/55">Cada factura muestra a qué servidor pertenece.</p>
            </div>
        </div>
    </div>

    @forelse ($invoices as $invoice)
    @php $related = cyber_invoice_services($invoice); @endphp
    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="block">
        <div class="cyber-card cyber-card-hover cyber-clip p-5 mb-4">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="bg-secondary/10 border border-secondary/25 p-2 rounded-lg">
                        <x-ri-bill-line class="size-5 text-secondary" />
                    </div>
                    <span class="font-bold">{{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice', ['id' => $invoice->id]) : __('invoices.invoice', ['id' => $invoice->number]) }}</span>
                    <x-ri-circle-fill class="size-1 text-base/20" />
                    <span class="text-base/70 font-mono font-semibold">{{ $invoice->formattedTotal }}</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($invoice->due_at && $invoice->status === 'pending')
                    <span class="text-xs px-2.5 py-1 rounded-md border border-warning/40 bg-warning/10 text-warning font-semibold">
                        Vence {{ $invoice->due_at->format('d/m/Y') }}
                    </span>
                    @endif
                    <div class="size-6 rounded-md p-0.5
                        @if ($invoice->status == 'paid') text-success bg-success/20
                        @elseif($invoice->status == 'cancelled') text-info bg-info/20
                        @else text-warning bg-warning/20
                        @endif">
                        @if ($invoice->status == 'paid')
                            <x-ri-checkbox-circle-fill />
                        @elseif($invoice->status == 'cancelled')
                            <x-ri-forbid-fill />
                        @else
                            <x-ri-error-warning-fill />
                        @endif
                    </div>
                </div>
            </div>

            @if(count($related) > 0)
            <div class="flex flex-wrap gap-2 mb-2">
                @foreach($related as $service)
                <span class="inline-flex items-center gap-1.5 text-xs rounded-md border border-primary/35 bg-primary/10 px-2.5 py-1 text-primary font-semibold">
                    <x-ri-instance-fill class="size-3.5" />
                    Esta factura es del servidor: {{ $service['label'] }}
                </span>
                @endforeach
            </div>
            @endif

            <div class="text-sm text-base/55">
                @foreach ($invoice->items as $item)
                <p>• {{ $item->description }}</p>
                @endforeach
                <p class="mt-1 text-xs text-base/40">{{ __('invoices.invoice_date') }}: {{ $invoice->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </a>
    @empty
    <div class="cyber-card cyber-clip p-8 text-center">
        <x-ri-inbox-2-line class="size-10 mx-auto text-base/30" />
        <p class="mt-3 text-base/60">{{ __('invoices.no_invoices') }}</p>
    </div>
    @endforelse

    {{ $invoices->links() }}
</div>
