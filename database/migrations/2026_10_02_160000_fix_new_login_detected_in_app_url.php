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
        DB::table('notification_templates')
            ->where('key', 'new_login_detected')
            ->where('in_app_url', '{{ route("profile.security") }}')
            ->update(['in_app_url' => '{{ route("account.security") }}']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('notification_templates')
            ->where('key', 'new_login_detected')
            ->where('in_app_url', '{{ route("account.security") }}')
            ->update(['in_app_url' => '{{ route("profile.security") }}']);
    }
};
