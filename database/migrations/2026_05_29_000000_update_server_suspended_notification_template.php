<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The original template body that shipped with Paymenter.
     * We only update templates that still match this exact default,
     * so admin-customized templates are never overwritten.
     */
    private const ORIGINAL_BODY = <<<'HTML'
                # Service suspended

                Your service has been suspended due to a payment failure.

                **Service details**
                - Name: {{ $service->product->name }}

                Please pay the invoice to reactivate the service.
                HTML;

    private const ORIGINAL_IN_APP_BODY = 'Your service {{ $service->product->name }} has been suspended due to a payment failure. Please pay the invoice to reactivate the service.';

    private const NEW_BODY = <<<'HTML'
                # Service suspended

                Your service has been suspended due to a payment failure.

                **Service details**
                - Name: {{ $service->product->name }}

                @isset($invoice)
                **Invoice details**
                - Invoice #: {{ $invoice->id }}
                - Amount due: **{{ $invoiceTotal }}**

                <div class="table">

                |   Item   | Quantity |  Price   |
                | :------: | :------: | :------: |
                @foreach ($invoiceItems as $item)
                | {{ $item->description }} | {{ $item->quantity }} | {{ $item->price }} |
                @endforeach
                </div>

                <div class="action">
                	<a class="button button-blue" href="{{ route('invoices.show', $invoice) }}">
                		Pay Invoice & Reactivate
                	</a>
                </div>
                @else
                Please pay the outstanding invoice to reactivate your service.

                <div class="action">
                	<a class="button button-blue" href="{{ route('invoices') }}">
                		View Invoices
                	</a>
                </div>
                @endisset
                HTML;

    private const NEW_IN_APP_BODY = 'Your service {{ $service->product->name }} has been suspended. @isset($invoice) Pay {{ $invoiceTotal }} to reactivate. @endisset';

    public function up(): void
    {
        // Only update if the template body still matches the original default.
        // This ensures admin-customized templates are never overwritten.
        DB::table('notification_templates')
            ->where('key', 'server_suspended')
            ->where('body', self::ORIGINAL_BODY)
            ->update([
                'body' => self::NEW_BODY,
                'in_app_body' => self::NEW_IN_APP_BODY,
            ]);
    }

    public function down(): void
    {
        DB::table('notification_templates')
            ->where('key', 'server_suspended')
            ->where('body', self::NEW_BODY)
            ->update([
                'body' => self::ORIGINAL_BODY,
                'in_app_body' => self::ORIGINAL_IN_APP_BODY,
            ]);
    }
};
