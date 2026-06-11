<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('invoices.ledger_adjustments') }} - {{ $adjustmentNote->number ?? $adjustmentNote->id }}</title>
    <style>
        body {
            font-family:
                system-ui,
                -apple-system,
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

        table tr td {
            padding: 0;
        }

        table tr td:last-child {
            text-align: right;
        }

        .info-table {
            font-size: 0.875em;
        }

        .info-table td {
            padding: 2px 0;
        }

        .section-title {
            margin-top: 50px;
            font-size: 1.2em;
            font-weight: bold;
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

    <!-- Adjustment Note type and status -->
    <div style="margin-bottom: 10px;font-size: 20px">
        <strong>{{ __('invoices.type') }}:</strong>
        <span style="@if($adjustmentNote->type === \App\Enums\AdjustmentNoteType::Credit->value) color: green; @else color: red; @endif">
            @if($adjustmentNote->type === \App\Enums\AdjustmentNoteType::Credit->value)
                {{ __('invoices.credit_note') }}
            @elseif($adjustmentNote->type === \App\Enums\AdjustmentNoteType::Debit->value)
                {{ __('invoices.debit_note') }}
            @endif
        </span>
    </div>
    <div style="margin-bottom: 20px;font-size: 20px">
        <strong>{{ __('invoices.status') }}:</strong>
        <span style="@if($adjustmentNote->status === \App\Enums\AdjustmentNoteStatus::Voided) color: red; @else color: green; @endif">
            {{ ucfirst($adjustmentNote->status instanceof \App\Enums\AdjustmentNoteStatus ? $adjustmentNote->status->value : $adjustmentNote->status) }}
        </span>
    </div>

    <table class="info-table">
        <tr>
            <td rowspan="2" style="font-size: 1em;vertical-align: top;">
                <strong>{{ __('invoices.issued_to') }}</strong><br>
                {{ $adjustmentNote->invoice->user_name }} <br />
                @foreach($adjustmentNote->invoice->user_properties as $property)
                    {{ $property }} <br />
                @endforeach
            </td>
            <td>
                <strong>{{ strtoupper(__('invoices.bill_to')) }}</strong> <br />
                {!! nl2br(e($adjustmentNote->invoice->bill_to)) !!}
            </td>
        </tr>
    </table>

    <p>{{ __('invoices.date') }}: <strong>{{ $adjustmentNote->created_at->format('d/m/Y') }}</strong></p>
    @if($adjustmentNote->number)
    <p>{{ __('invoices.invoice_no') }}: <strong>{{ $adjustmentNote->number }}</strong></p>
    @endif
    <p>{{ __('invoices.invoice') }}: <strong>{{ $adjustmentNote->invoice->number ?? $adjustmentNote->invoice->id }}</strong></p>

    <div class="section-title">{{ __('invoices.ledger_adjustments') }}</div>
    <table style="margin-top: 10px;">
        <thead>
            <tr>
                <th>{{ __('invoices.date') }}</th>
                <th>{{ __('invoices.type') }}</th>
                <th>{{ __('invoices.description') }}</th>
                <th>{{ __('invoices.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $adjustmentNote->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($adjustmentNote->type === \App\Enums\AdjustmentNoteType::Credit->value)
                    {{ __('invoices.credit_note') }}
                    @elseif($adjustmentNote->type === \App\Enums\AdjustmentNoteType::Debit->value)
                    {{ __('invoices.debit_note') }}
                    @endif
                </td>
                <td>{{ $adjustmentNote->description }}</td>
                <td>{{ $adjustmentNote->formattedAmount }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
