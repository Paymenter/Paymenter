<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice_transactions', function (Blueprint $table) {
            // Change gateway_id to be null on delete
            $table->dropForeign(['gateway_id']);
            $table->foreign('gateway_id')->references('id')->on('extensions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_transactions', function (Blueprint $table) {
            $table->dropForeign(['gateway_id']);
            $table->foreign('gateway_id')->references('id')->on('extensions')->cascadeOnDelete();
        });
    }
};
