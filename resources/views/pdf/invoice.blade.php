<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice', ['id' => $invoice->id]) : __('invoices.invoice', ['id' => $invoice->number]) }}</title>
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
                'Segoe UI Emoji',
                'DejaVu Sans';
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

        table tr td:last-child {
            text-align: right;
        }

        .invoice-info {
            font-size: 0.875em;
        }

        .invoice-info td {
            padding: 2px 0;
        }

        .invoice-items td {
            padding: 10px 16px 10px 0;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .invoice-items th {
            padding-right: 16px;
        }

        .invoice-items th:last-child,
        .invoice-items td:last-child {
            padding-right: 0;
            white-space: nowrap;
            min-width: 70px;
        }

        .totals-section {
            margin-top: 30px;
            float: right;
            width: 300px;
        }

        .totals-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .totals-table td {
            padding: 8px 0;
            border: none;
        }

        .totals-table .label {
            text-align: left;
            font-weight: normal;
            color: #666;
            text-transform: uppercase;
            font-size: 0.875em;
        }

        .totals-table .amount {
            text-align: right;
            font-weight: bold;
        }

        .totals-table .total-row {
            border-top: 2px solid #ddd;
            font-size: 1.1em;
        }

        .totals-table .total-row .label {
            font-weight: bold;
            color: #000;
        }

        .invoice-items td p {
            margin: 0 0 4px 0;
            padding: 0;
        }

        .invoice-items td p:last-child {
            margin-bottom: 0;
        }

        .invoice-items td ul,
        .invoice-items td ol {
            margin: 4px 0;
            padding-left: 16px;
        }

        .invoice-items td.item-description {
            font-size: 0.8em;
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
                {{ $invoice->user_name }} <br />
                @foreach($invoice->user_properties as $property)
                    {{ $property }} <br />
                @endforeach
            </td>
            <td>
                <strong>{{ strtoupper(__('invoices.bill_to')) }}</strong> <br />
                {!! nl2br(e($invoice->bill_to)) !!}
            </td>
        </tr>
    </table>
    <p>{{ !$invoice->number && config('settings.invoice_proforma', false) ? __('invoices.proforma_invoice_date') : __('invoices.invoice_date') }}: <strong>{{ $invoice->created_at->format('d/m/Y') }}</strong></p>
    @if($invoice->number)
    <p>{{ __('invoices.invoice_no') }}: <strong>{{ $invoice->number }}</strong></p>
    @endif

    @php
        $visibleItems = $invoice->items->filter(fn ($item) => $item->price >= 0);
        $showQtyColumns = $visibleItems->some(fn ($i) => $i->quantity != 1 || !empty($i->unit));
    @endphp
    <table style="margin-top: 40px;" class="invoice-items">
        <thead>
            <tr>
                <th>{{ __('invoices.item') }}</th>
                @if($showQtyColumns)
                <th style="width: 100px">{{ __('invoices.quantity') }}</th>
                <th>{{ __('invoices.unit_price') }}</th>
                @endif
                <th>{{ __('invoices.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visibleItems as $item)
            @php
                $descHtml = \Illuminate\Support\Str::markdown($item->description ?? '', ['html_input' => 'strip', 'renderer' => ['soft_break' => "<br>\n"]]);
                $descHtml = trim(preg_replace('/<\/?p>/', '', $descHtml));
                $descHtml = preg_replace('/(<br>\s*)+$/', '', $descHtml);
            @endphp
            <tr>
                <td class="item-description">{!! $descHtml !!}</td>
                @if($showQtyColumns)
                <td>{{ $item->quantity }}{{ $item->unit ? ' ' . $item->unit : '' }}</td>
                <td>{{ $item->formattedPrice }}</td>
                @endif
                <td>{{ $item->formattedTotal }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $beforeTaxDiscountItems = $invoice->items->filter(fn ($i) => $i->price < 0 && ! $i->apply_after_tax);
        $afterTaxDiscountItems = $invoice->items->filter(fn ($i) => $i->price < 0 && $i->apply_after_tax);
        $positiveItemsTotal = $visibleItems->sum(fn ($i) => $i->price * $i->quantity);
        $hasBeforeDiscount = $beforeTaxDiscountItems->isNotEmpty();
        $hasTax = $invoice->formattedTotal->tax > 0;
    @endphp
    <div class="totals-section">
        <table class="totals-table">
            @if ($hasBeforeDiscount)
            <tr>
                <td class="label">{{ __('invoices.subtotal') }}</td>
                <td class="amount">{{ $invoice->formattedTotal->format($positiveItemsTotal) }}</td>
            </tr>
            @foreach ($beforeTaxDiscountItems as $discountItem)
            <tr>
                <td class="label">{{ $discountItem->description }}</td>
                <td class="amount">{{ $discountItem->formattedTotal }}</td>
            </tr>
            @endforeach
            @endif
            @if ($hasTax)
            <tr>
                <td class="label">{{ $hasBeforeDiscount ? __('invoices.net') : __('invoices.subtotal') }}</td>
                <td class="amount">{{ $invoice->formattedTotal->format($invoice->formattedTotal->subtotal) }}</td>
            </tr>
            <tr>
                <td class="label">{{ $invoice->tax->name }} ({{ $invoice->tax->rate }}%)</td>
                <td class="amount">{{ $invoice->formattedTotal->formatted->tax }}</td>
            </tr>
            @endif
            @foreach ($afterTaxDiscountItems as $discountItem)
            <tr>
                <td class="label">{{ $discountItem->description }}</td>
                <td class="amount">{{ $discountItem->formattedTotal }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td class="label">{{ __('invoices.total') }}</td>
                <td class="amount">{{ $invoice->formattedGrandTotal }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($invoice->transactions->where('status', \App\Enums\InvoiceTransactionStatus::Succeeded)->count() > 0)
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
            @foreach($invoice->transactions->where('status', \App\Enums\InvoiceTransactionStatus::Succeeded) as $transaction)
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
