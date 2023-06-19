<x-app-layout title="Invoices" clients>
    <x-success class="mt-4" />
    <div class="content">
        <div class="content-box content-box !p-0 overflow-hidden">
            <h2 class="text-xl font-semibold p-6">{{ __('Invoices') }}</h2>
            @if ($invoices->count() > 0)
                <table class="w-full">
                    <thead class="border-b-2 border-secondary-200 dark:border-secondary-50 text-secondary-600">
                        <tr>
                            <th scope="col" class="text-start pl-6 py-2 text-sm font-normal">
                                {{ __('Total') }}
                            </th>
                            <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                                {{ __('Created Date') }}</th>

                            <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                                {{ __('Status') }}</th>
                            <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                                {{ __('View') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices->sortByDesc('status') as $invoice)
                            @if ($invoice->items->count() == 0)
                                @continue
                            @endif
                            <tr class="border-b-2 border-secondary-200 dark:border-secondary-50">
                                <td class="pl-6 py-3">
                                    {{ config('settings::currency_sign') }}{{ $invoice->total() }}
                                </td>
                                <td class="pr-6 py-3">
                                    {{ $invoice->created_at->format('d M Y') }}
                                </td>
                                <td class="pr-6 py-3">
                                    {{ ucfirst($invoice->status) }}
                                </td>
                                <td class="pr-6 py-3">
                                    <a href="{{ route('clients.invoice.show', $invoice->id) }}"
                                        class="button button-secondary">
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>
    </div>
    </div>
</x-app-layout>
