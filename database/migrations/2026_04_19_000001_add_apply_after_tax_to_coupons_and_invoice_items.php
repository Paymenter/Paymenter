<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->boolean('apply_after_tax')->default(false)->after('recurring');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->boolean('apply_after_tax')->default(false)->after('reference_type');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('apply_after_tax');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('apply_after_tax');
        });
    }
};
