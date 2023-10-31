<x-app-layout title="Invoices" clients>
    <x-success class="mt-4" />
    <div class="content">
        <div class="content-box content-box !p-0 overflow-hidden">
            <h2 class="text-xl font-semibold p-6">{{ __('Invoices') }}</h2>
            @if ($invoices->count() > 0)
                <table class="w-full">
                    <thead class="border-b-2 border-secondary-200 text-secondary-600">
                    <tr>
                        <th scope="col" class="text-start pl-6 py-2 text-sm font-normal">
                            {{ __('ID')}}
                        </th>
                        <th scope="col" class="text-start pl-6 py-2 text-sm font-normal">
                            {{ __('Total') }}
                        </th>
                        <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                            {{ __('Created Date') }}
                        </th>
                        <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="text-start pr-6 py-2 text-sm font-normal">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($invoices->sortByDesc('status') as $invoice)
                        @if ($invoice->items->count() == 0)
                            @continue
                        @endif
                        <tr class="@if(($loop->index + 1) < $loop->count) border-b-2 border-secondary-200 @endif">
                            <td class="pl-6 py-3">
                                {{ $invoice->id }}
                            </td>
                            <td class="pl-6 py-3">
                                <x-money :amount="$invoice->total()" />
                            </td>
                            <td class="pr-6 py-3">
                                {{ $invoice->created_at }}
                            </td>
                            <td class="pr-6 py-3">
                                @if (ucfirst($invoice->status) == 'Pending')
                                    <span class="text-red-400 font-semibold">
                                        {{ __('Pending') }}
                                    </span>
                                @elseif (ucfirst($invoice->status) == 'Paid')
                                    <span class="text-green-400 font-semibold">
                                        {{__('Paid')}}
                                    </span>
                                @elseif (ucfirst($invoice->status) == 'Cancelled')
                                    <span class="text-orange-400 font-semibold">
                                        {{__('Cancelled')}}
                                    </span>
                                @else
                                    <span class="text-gray-400 font-semibold">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="pr-6 py-3">
                                <a href="{{ route('clients.invoice.show', $invoice->id) }}"
                                   class="button button-secondary">
                                    <i class="ri-eye-line"></i> {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
