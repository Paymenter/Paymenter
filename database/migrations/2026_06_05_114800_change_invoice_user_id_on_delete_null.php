<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $foreignKeyExists = DB::table('information_schema.TABLE_CONSTRAINTS')
                ->whereRaw('CONSTRAINT_SCHEMA = DATABASE()')
                ->where('TABLE_NAME', 'invoices')
                ->where('CONSTRAINT_NAME', 'invoices_user_id_foreign')
                ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
                ->exists();

            if ($foreignKeyExists) {
                $table->dropForeign('invoices_user_id_foreign');
            }

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
            $foreignKeyExists = DB::table('information_schema.TABLE_CONSTRAINTS')
                ->whereRaw('CONSTRAINT_SCHEMA = DATABASE()')
                ->where('TABLE_NAME', 'invoices')
                ->where('CONSTRAINT_NAME', 'invoices_user_id_foreign')
                ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
                ->exists();

            if ($foreignKeyExists) {
                $table->dropForeign('invoices_user_id_foreign');
            }

            $table->foreignIdFor(User::class)
                ->nullable(false)
                ->change();
        });
    }
};
