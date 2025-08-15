<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('email_templates')->insert([
            [
                'key' => 'insufficient_credits',
                'subject' => 'Insufficient Credits for Invoice Payment',
                'body' => '<p>Hello {{ $user->name }},</p><p>We tried to pay your invoice #{{ $invoice->number }} using your credits, but you have insufficient credits ({{ $credit->amount }} {{ $credit->currency_code }}).</p><p>Please add more credits or pay the invoice manually.</p>',
                'enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_mismatch',
                'subject' => 'Currency Mismatch for Invoice Payment',
                'body' => '<p>Hello {{ $user->name }},</p><p>We tried to pay your invoice #{{ $invoice->number }} using your credits, but you do not have credits in the required currency ({{ $invoice->currency_code }}).</p><p>Please add credits in the correct currency or pay the invoice manually.</p>',
                'enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('email_templates')->whereIn('key', ['insufficient_credits', 'currency_mismatch'])->delete();
    }
};
