@props(['invoice'])

@php
    $statusClass = match($invoice->status) {
        'paid'      => 'text-success bg-success/20',
        'cancelled' => 'text-info bg-info/20',
        default     => 'text-warning bg-warning/20',
    };

    $visibleItems = $invoice->items->filter(fn($i) => $i->price >= 0);
    $first        = $visibleItems->first();
    $extraCount   = $visibleItems->count() - 1;
    $plainDesc    = $first ? strip_tags(\Illuminate\Support\Str::markdown($first->description ?? '', ['html_input' => 'strip'])) : '';
@endphp

<x-entity-card :href="route('invoices.show', $invoice)" :statusClass="$statusClass">
    <x-slot:icon><x-ri-bill-line class="size-5 text-secondary" /></x-slot:icon>

    <x-slot:heading>
        <span class="font-medium">
            {{ !$invoice->number && config('settings.invoice_proforma', false)
                ? __('invoices.proforma_invoice', ['id' => $invoice->id])
                : __('invoices.invoice', ['id' => $invoice->number]) }}
        </span>
        <x-ri-circle-fill class="size-1 text-base/20" />
        <span class="text-base text-sm">{{ $invoice->formattedGrandTotal }}</span>
    </x-slot:heading>

    <x-slot:status>
        @if ($invoice->status == 'paid')
            <x-ri-checkbox-circle-fill />
        @elseif($invoice->status == 'cancelled')
            <x-ri-forbid-fill />
        @else
            <x-ri-error-warning-fill />
        @endif
    </x-slot:status>

    <x-slot:detail>
        @if($first)
            <p class="text-base/50 text-sm line-clamp-2 mt-1">{{ $plainDesc }}</p>
            @if($extraCount > 0)
                <p class="text-base/40 text-xs mt-0.5">{{ trans_choice(__('invoices.more_items'), $extraCount, ['count' => $extraCount]) }}</p>
            @endif
        @endif
    </x-slot:detail>
</x-entity-card>
