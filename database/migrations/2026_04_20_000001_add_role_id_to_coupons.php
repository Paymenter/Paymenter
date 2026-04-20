<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete()->after('apply_after_tax');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Role::class);
            $table->dropColumn('role_id');
        });
    }
};
