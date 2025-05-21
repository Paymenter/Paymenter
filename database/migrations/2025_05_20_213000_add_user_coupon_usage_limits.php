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
        // Add max_uses_per_user to coupons table
        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('max_uses_per_user')->nullable()->after('max_uses');
        });

        // Add coupon_usage JSON column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->json('coupon_usage')->nullable()->after('tfa_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove max_uses_per_user from coupons table
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('max_uses_per_user');
        });

        // Remove coupon_usage from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('coupon_usage');
        });
    }
};
