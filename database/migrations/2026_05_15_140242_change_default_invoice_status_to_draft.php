<?php

use App\Models\User;
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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();

            $table->string('cancellation_reason')->nullable()->default(null)->after('status');

            $table->dropForeign('invoices_user_id_foreign');

            $table->foreignIdFor(User::class)
                ->nullable()
                ->change();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_user_id_foreign');

            $table->foreignIdFor(User::class)
                ->nullable(false)
                ->change();

            $table->dropColumn('cancellation_reason');

            $table->string('status')->default('pending')->change();
        });
    }
};
