<div class="space-y-4">
    <div class="text-lg font-bold pb-4">{{ __('invoices.invoices') }}</div>

    @foreach ($invoices as $invoice)
    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate>
        <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
            <div class="bg-secondary/10 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-secondary" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 8L9.00319 2H19.9978C20.5513 2 21 2.45531 21 2.9918V21.0082C21 21.556 20.5551 22 20.0066 22H3.9934C3.44476 22 3 21.5501 3 20.9932V8ZM10 4V9H5V20H19V4H10Z"></path>
                </svg>
            </div>
            <span class="font-medium">Invoice #{{$invoice->id }}</span>
            <span class="text-base/50 font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-1 h-1 text-base/20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"></path>
                </svg>
            </span>
            <span class="text-base text-sm">{{ $invoice->formattedTotal }}</span>
            </div>
            <div class="w-5 h-5 rounded-md p-0.5
                @if ($invoice->status == 'paid') text-success bg-success/20 
                @elseif($invoice->status == 'cancelled') text-info bg-info/20
                @else text-warning bg-warning/20 
                @endif">
                @if ($invoice->status == 'paid')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM11.0026 16L18.0737 8.92893L16.6595 7.51472L11.0026 13.1716L8.17421 10.3431L6.75999 11.7574L11.0026 16Z"></path>
                    </svg>
                @elseif($invoice->status == 'cancelled')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM8.52313 7.10891C8.25459 7.30029 7.99828 7.51644 7.75736 7.75736C7.51644 7.99828 7.30029 8.25459 7.10891 8.52313L15.4769 16.8911C15.7454 16.6997 16.0017 16.4836 16.2426 16.2426C16.4836 16.0017 16.6997 15.7454 16.8911 15.4769L8.52313 7.10891Z"></path>
                    </svg>
                @elseif($invoice->status == 'pending')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM11 15V17H13V15H11ZM11 7V13H13V7H11Z"></path>
                    </svg>
                @endif
            </div>
        </div>
        @foreach ($invoice->items as $item)
            <p class="text-base text-sm">Item(s): {{ $item->description }} ({{ __('invoices.invoice_date')}}: {{ $invoice->created_at->format('d M Y') }})</p>
        @endforeach
        </div>
    </a>
    @endforeach
</div>