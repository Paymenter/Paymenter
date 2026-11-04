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
        // Services kept the id of soft-deleted billing agreements, so renewals kept trying to charge them
        DB::table('services')
            ->whereIn('billing_agreement_id', DB::table('billing_agreements')->select('id')->whereNotNull('deleted_at'))
            ->update(['billing_agreement_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
