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
        Setting::updateOrCreate(
            ['key' => 'notes_client_visible'],
            [
                'value' => 'true',
                'settingable_type' => null,
                'type' => 'boolean',
                'encrypted' => false,
            ],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('key', 'notes_client_visible')->delete();
    }
};
