<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('invoices.invoice', ['id' => $invoice->number]) }}</title>
    <style>
        body {
            font-family:
                system-ui,
                -apple-system,
                /* Firefox supports this but not yet `system-ui` */
                'Segoe UI',
                Roboto,
                Helvetica,
                Arial,
                sans-serif,
                'Apple Color Emoji',
                'Segoe UI Emoji';
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            text-align: left;
            color: #999;
            border-bottom: 2px solid #ddd;
            padding: 10px 0 15px 0;
            font-size: 0.75em;
            text-transform: uppercase;
        }

        table td {
            padding: 15px 0;
        }

        table th:last-child {
            text-align: right;
        }

        table tr td {
            padding: 0;
        }

        table tr td:last-child {
            text-align: right;
        }

        .invoice-items tbody tr:first-child td {
            padding-top: 10px;
        }

        .invoice-info {
            font-size: 0.875em;
        }

        .invoice-info td {
            padding: 2px 0;
        }
    </style>
</head>

<body>
    @if(config('settings.logo'))
    <div style="margin: 20px 0 70px 0;">
        <img style="height: 30px" src="{{ public_path('storage/' . config('settings.logo')) }}"
            alt="{{ config('app.name') }}">
    </div>
    @endif

    <!-- Invoice status -->
    <div style="margin-bottom: 20px;font-size: 20px">
        <strong>{{ __('invoices.status') }}:</strong><span
            style="@if($invoice->status == 'paid') color: green; @else color: orange; @endif">
            {{ ucfirst($invoice->status) }}
        </span>
    </div>

    <table class="invoice-info">
        <tr>
            <td rowspan="2" style="font-size: 1em;vertical-align: top;">
                <strong>{{ __('invoices.issued_to') }}</strong><br>
                {{ $invoice->user->name }} <br />
                @foreach($invoice->user->properties()->with('parent_property')->whereHas('parent_property', function
                ($query) {
                $query->where('show_on_invoice', true);
                })->get() as $property)
                {{ $property->value }} <br />
                @endforeach
            </td>
            <td>
                <strong>{{ strtoupper(__('invoices.bill_to')) }}</strong> <br />
                {{ config('settings.company_name') }} <br />
                {{ config('settings.company_address') }} {{ config('settings.company_address2') }} <br />
                {{ config('settings.company_zip') }} {{ config('settings.company_city') }} <br />
                {{ config('settings.company_state') }} {{ config('settings.company_country') }}  <br />
                {{ config('settings.company_tax_id') }} <br />
                {{ config('settings.company_id') }} <br />
            </td>
        </tr>
    </table>
    <p>{{ __('invoices.invoice_date') }}: <strong>{{ $invoice->created_at->format('d/m/Y') }}</strong></p>
    <p>{{ __('invoices.invoice_no') }}: <strong>{{ $invoice->number }}</strong></p>

    <table style="margin-top: 40px;" class="invoice-items">
        <thead>
            <tr>
                <th>{{ __('invoices.item') }}</th>
                <th style="width: 100px">{{ __('invoices.quantity') }}</th>
                <th>{{ __('invoices.unit_price') }}</th>
                <th>{{ __('invoices.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->formattedPrice }}</td>
                <td>{{ $item->formattedTotal }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($invoice->transactions->count() > 0)
    <table style="margin-top: 80px;" class="invoice-items">
        <thead>
            <tr>
                <th>{{ __('invoices.transaction_id') }}</th>
                <th>{{ __('invoices.payment_date') }}</th>
                <th>{{ __('invoices.amount') }}</th>
                <th>{{ __('invoices.payment_method') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->transactions as $transaction)
            <tr>
                <td>{{ $transaction->transaction_id }}</td>
                <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                <td>{{ $transaction->formattedAmount }}</td>
                <td>{{ $transaction->gateway ? $transaction->gateway->name : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>

</html>