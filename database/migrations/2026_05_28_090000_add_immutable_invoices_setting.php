<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Setting::count() > 0) {
            Setting::updateOrCreate(
                ['key' => 'immutable_invoices_enabled'],
                [
                    'value' => 'false',
                    'settingable_type' => null,
                    'type' => 'boolean',
                    'encrypted' => false,
                ],
            );

            Setting::updateOrCreate(
                ['key' => 'immutable_invoices_lock_date'],
                [
                    'value' => Carbon::now()->toDateString(),
                    'settingable_type' => null,
                    'type' => 'date',
                    'encrypted' => false,
                ],
            );

            Setting::updateOrCreate(
                ['key' => 'immutable_invoices_lock_before'],
                [
                    'value' => 'false',
                    'settingable_type' => null,
                    'type' => 'boolean',
                    'encrypted' => false,
                ],
            );
        }

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
        Setting::whereIn('key', [
            'immutable_invoices_enabled',
            'immutable_invoices_lock_before',
            'immutable_invoices_lock_date',
            'notes_client_visible',
        ])->delete();
    }
};
