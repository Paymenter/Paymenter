<div class="container mt-14 space-y-4">
    <x-navigation.breadcrumb />

    @forelse ($invoices as $invoice)
        <x-invoice-card :invoice="$invoice" />
    @empty
    <div class="bg-background-secondary border border-neutral p-4 rounded-lg">
        <p class="text-base text-sm">{{ __('invoices.no_invoices') }}</p>
    </div>
    @endforelse

    {{ $invoices->links() }}
</div>
