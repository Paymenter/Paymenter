<div class="bg-primary-800 p-6 rounded-lg mt-2">
    <div class="flex flex-col md:flex-row justify-between">
        <h1 class="text-2xl font-semibold text-white">{{ __('invoices.invoices') }}</h1>
    </div>
    <div class="w-full overflow-x-auto mt-2">

        <table class="w-full border-spacing-y-2.5 border-separate">
            <thead>
                <tr>
                    <th class="text-left pl-2">{{ __('invoices.id') }}</th>
                    <th class="text-left">{{ __('invoices.total') }}</th>
                    <th class="text-left">{{ __('invoices.status') }}</th>
                    <th class="text-left">{{ __('invoices.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                <tr class="bg-primary-700 text-white hover:shadow-xl transition duration-300">
                    <td class="p-2 rounded-l-md">#{{ $invoice->id }}</td>
                    <td>{{ $invoice->formattedTotal }}</td>
                    <td>
                        <span
                            class="font-semibold p-1 px-1.5 rounded-md @if ($invoice->status == 'paid') text-green-500 @elseif($invoice->status == 'cancelled') text-red-500  @else text-orange-500 @endif">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td class="p-1 rounded-r-md">
                        <a href="{{ route('invoices.show', $invoice) }}" wire:navigate>
                            <x-button.primary class="h-fit !w-fit">
                                {{ __('invoices.view') }}
                            </x-button.primary>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $invoices->links() }}

</div>