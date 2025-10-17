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
        Schema::table('custom_properties', function (Blueprint $table) {
            $table->string('condition_mode', 16)->default('none')->after('show_on_invoice');
            $table->json('condition_rules')->nullable()->after('condition_mode');
            $table->integer('sort_order')->default(0)->after('condition_rules');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_properties', function (Blueprint $table) {
            $table->dropColumn(['condition_mode', 'condition_rules', 'sort_order']);
        });
    }
};
