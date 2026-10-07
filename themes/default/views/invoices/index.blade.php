<div class="container mt-14 space-y-4">
    <x-navigation.breadcrumb />

    <section class="bg-background-secondary border border-neutral rounded-lg p-4 sm:p-6" aria-label="{{ __('invoices.filters') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <x-form.select name="status" :label="__('invoices.status')" wire:model.live="status">
                <option value="">{{ __('invoices.all_statuses') }}</option>
                <option value="pending">{{ __('invoices.pending') }}</option>
                <option value="paid">{{ __('invoices.paid') }}</option>
                <option value="cancelled">{{ __('invoices.cancelled') }}</option>
            </x-form.select>
            <x-form.input name="dateFrom" type="date" :label="__('invoices.issued_from')" wire:model.live="dateFrom" />
            <x-form.input name="dateUntil" type="date" :label="__('invoices.issued_until')" wire:model.live="dateUntil" />
            <button type="button" wire:click="clearFilters" class="text-sm font-semibold underline underline-offset-2 text-left pb-2.5">
                {{ __('invoices.clear_filters') }}
            </button>
        </div>
        @error('dateFrom') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
        @error('dateUntil') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
    </section>

    @forelse ($invoices as $invoice)
        <article class="bg-background-secondary border border-neutral rounded-lg p-4 sm:p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="space-y-2 min-w-0">
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="bg-secondary/10 p-2 rounded-lg shrink-0">
                            <x-ri-bill-line class="size-5 text-secondary" />
                        </div>
                        <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="font-semibold hover:underline underline-offset-2">
                            {{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice', ['id' => $invoice->id]) : __('invoices.invoice', ['id' => $invoice->number]) }}
                        </a>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                            @if ($invoice->status === 'paid') text-success bg-success/20
                            @elseif ($invoice->status === 'cancelled') text-info bg-info/20
                            @else text-warning bg-warning/20
                            @endif">
                            {{ __('invoices.' . ($invoice->status === 'pending' ? 'pending' : $invoice->status)) }}
                        </span>
                    </div>
                    <p class="text-sm text-base/70">{{ __('invoices.invoice_date') }}: {{ $invoice->created_at->format('d M Y') }}</p>
                    @if ($invoice->due_at)
                        <p class="text-sm {{ $invoice->status === 'pending' && $invoice->due_at->isBefore(today()) ? 'text-warning font-semibold' : 'text-base/70' }}">
                            {{ __('invoices.due_date') }}: {{ $invoice->due_at->format('d M Y') }}
                            @if ($invoice->status === 'pending' && $invoice->due_at->isBefore(today()))
                                <span>({{ __('invoices.overdue') }})</span>
                            @endif
                        </p>
                    @endif
                </div>

                <div class="sm:text-right space-y-1 shrink-0">
                    <p class="text-sm text-base/70">{{ __('invoices.total') }}</p>
                    <p class="font-semibold">{{ $invoice->formattedTotal }}</p>
                    @if ($invoice->status === 'pending')
                        <p class="text-sm text-base/70">{{ __('invoices.amount_due', ['amount' => $invoice->formattedRemaining]) }}</p>
                    @endif
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-neutral pt-4">
                <p class="text-sm text-base/70 truncate">
                    {{ $invoice->items->pluck('description')->filter()->take(2)->join(', ') }}
                </p>
                <div class="flex gap-3 sm:shrink-0">
                    <a href="{{ route('invoices.show', $invoice) }}" wire:navigate class="text-sm font-semibold underline underline-offset-2 py-2">{{ __('invoices.view') }}</a>
                    @if ($invoice->status === 'pending')
                        <a href="{{ route('invoices.show', $invoice) }}?pay" wire:navigate class="inline-flex items-center justify-center gap-2 rounded-md bg-primary text-white text-sm font-semibold hover:bg-primary/80 px-4 py-2.5 duration-300">
                            {{ __('invoices.pay_now') }}
                        </a>
                    @endif
                </div>
            </div>
        </article>
    @empty
        <div class="bg-background-secondary border border-neutral rounded-lg p-5">
            <p class="text-sm">{{ __('invoices.no_invoices') }}</p>
        </div>
    @endforelse

    {{ $invoices->links() }}
</div>
