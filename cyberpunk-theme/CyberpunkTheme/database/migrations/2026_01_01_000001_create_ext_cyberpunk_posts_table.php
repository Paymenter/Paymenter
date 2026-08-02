<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_cyberpunk_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('content');
            $table->boolean('approved')->default(true);
            $table->boolean('pinned')->default(false);
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->timestamps();

            $table->index(['approved', 'pinned', 'created_at']);
        });

        Schema::create('ext_cyberpunk_post_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('ext_cyberpunk_posts')->cascadeOnDelete();
            $table->string('type')->default('image'); // image | video
            $table->string('path');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_cyberpunk_post_media');
        Schema::dropIfExists('ext_cyberpunk_posts');
    }
};
