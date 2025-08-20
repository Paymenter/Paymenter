<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('number')->nullable()->unique()->after('id');
        });

        DB::statement('UPDATE invoices SET number = id');

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('number')->nullable(false)->change();
        });

        // Set the default value for invoice_number to the current max value
        Setting::withoutEvents(function () {
            Setting::updateOrCreate([
                'key' => 'invoice_number',
            ], [
                'value' => DB::table('invoices')->max('id') ?: 0,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('number');
        });
    }
};
