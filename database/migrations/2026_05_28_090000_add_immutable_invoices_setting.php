<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Setting::count() > 0) {
            Setting::createOrUpdate([
                'key' => 'immutable_invoices_enabled',
                'value' => 'false',
                'settingable_type' => null,
                'type' => 'boolean',
                'encrypted' => false,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('key', 'immutable_invoices_enabled')->delete();
    }
};
