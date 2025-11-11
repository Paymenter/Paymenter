<?php

use App\Models\Currency;
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
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('name')->after('code')->nullable();
        });

        // Set name for existing currencies
        Currency::each(function ($currency) {
            $currency->name = $currency->code;
            $currency->save();
        });

        Schema::table('currencies', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
