<div class="space-y-4">
    <x-navigation.breadcrumb />

    @else ($invoices as $invoice)
    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate>
        <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
            <div class="bg-secondary/10 p-2 rounded-lg">
                <x-ri-bill-line class="size-5 text-secondary" />
            </div>
            <span class="font-medium">{{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice', ['id' => $invoice->id]) : __('invoices.invoice', ['id' => $invoice->number]) }}</span>
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
            <p class="text-base text-sm">Item(s): {{ $item->description }} ({{ __('invoices.invoice_date')}}: {{ $invoice->created_at->format('d M Y') }})</p>
        @endforeach
        </div>
    </a>
<div class="space-y-4">
    <div class="flex flex-row justify-between">
        <x-navigation.breadcrumb />
        <x-navigation.link :href="route('tickets.create')" class="flex items-center gap-2">
            <x-ri-add-line class="size-5" />
            <span>{{ __('ticket.create_ticket') }}</span>
        </x-navigation.link>
    </div>
    @forelse ($tickets as $ticket)
    <a href="{{ route('tickets.show', $ticket) }}" wire:navigate>
        <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="bg-secondary/10 p-2 rounded-lg">
                        <x-ri-ticket-line class="size-5 text-secondary" />
                    </div>
                    <span class="font-medium">{{ $ticket->subject }}</span>
                </div>
                <div class="size-5 rounded-md p-0.5
                    @if ($ticket->status == 'open') text-success bg-success/20 
                    @elseif($ticket->status == 'closed') text-inactive bg-inactive/20
                    @else text-info bg-info/20 
                    @endif"
                    @if ($ticket->status == 'open')
                        <x-ri-add-circle-fill />
                    @elseif($ticket->status == 'closed')
                        <x-ri-forbid-fill />
                    @elseif($ticket->status == 'replied')
                        <x-ri-chat-smile-2-fill />
                    @endif
                </div>
            </div>
            <p class="text-base text-sm">
                {{ __('ticket.last_activity') }}
                {{ $ticket->messages()->orderBy('created_at', 'desc')->first()->created_at->diffForHumans() }}
                {{ $ticket->department ? ' - ' . $ticket->department : '' }}
            </p>
        </div>
    </a>
    @empty
        <div class="text-center p-6 text-neutral/70 border border-neutral/20 rounded-lg">
            No invoices found.
        </div>
    @endforelse
</div>

    {{ $invoices->links() }}
</div>
