<div class="space-y-4">
    @foreach ($invoices as $invoice)
        <x-invoice-card :invoice="$invoice" />
    @endforeach
</div>
