<div class="space-y-4">
    @foreach ($invoices as $invoice)
    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate>
        <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="bg-secondary/10 p-2 rounded-lg">
                        <x-ri-bill-line class="size-5 text-secondary" />
                    </div>
                    <span class="font-medium">Invoice #{{$invoice->number }}</span>
                    <span class="text-base/50 font-semibold">
                        <x-ri-circle-fill class="size-1 text-base/20" />
                    </span>
                    <span class="text-base text-sm">{{ $invoice->formattedTotal }}</span>
                </div>
                <div class="size-5 rounded-md p-0.5
                @if ($invoice->status == 'paid') text-success bg-success/20
                @elseif($invoice->status == 'cancelled') text-info bg-info/20
                @else text-warning bg-warning/20
                @endif">
                @if ($invoice->status == 'paid')
                    <x-ri-checkbox-circle-fill />
                @elseif($invoice->status == 'cancelled')
                    <x-ri-forbid-fill />
                @elseif($invoice->status == 'pending')
                    <x-ri-error-warning-fill />
                @endif
            </div>
            </div>
            @foreach ($invoice->items as $item)
            <p class="text-base text-sm text-wrap">Item(s): {{ $item->description }} ({{ __('invoices.invoice_date')}}: {{
                $invoice->created_at->format('d M Y') }})</p>
            @if(in_array($item->reference_type, ['App\Models\Service', 'App\Models\ServiceUpgrade']))
                @if($item->reference_type == 'App\Models\Service' && $item->reference)
                    @php
                        $hostname = $item->reference->getHostnameAttribute();
                    @endphp
                @elseif($item->reference_type == 'App\Models\ServiceUpgrade' && $item->reference && $item->reference->service)
                    @php
                        $hostname = $item->reference->service->getHostnameAttribute();
                    @endphp
                @endif
                @if($hostname)
                    <p class="text-base/60 text-sm">{{ __('services.hostname') }}: {{ $hostname }}</p>
                @endif
            @endif
            @endforeach
        </div>
    </a>
    @endforeach
</div>
