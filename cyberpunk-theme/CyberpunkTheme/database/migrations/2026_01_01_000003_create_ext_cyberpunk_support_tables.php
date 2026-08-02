<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_cyberpunk_avatars', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->unique()->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->timestamps();
        });

        Schema::create('ext_cyberpunk_visits', function (Blueprint $table) {
            $table->id();
            $table->date('day')->unique();
            $table->unsignedBigInteger('count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_cyberpunk_visits');
        Schema::dropIfExists('ext_cyberpunk_avatars');
    }
};
