<?php

use App\Models\{User, Role};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->unsignedBigInteger('role_id')->nullable()->after('email');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
        User::all()->each(function ($user) {
            // If user is admin, set permissions to all
            if ($user->is_admin) {
                $user->role_id = 1;
            } else {
                $user->role_id = 2;
            }
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
            $table->dropColumn('permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
            $table->json('permissions')->nullable();
        });
    }
};
