<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_cyberpunk_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->morphs('commentable'); // posts y productos
            $table->foreignId('parent_id')->nullable()->constrained('ext_cyberpunk_comments')->cascadeOnDelete();
            $table->text('content');
            $table->boolean('approved')->default(true);
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamps();

            $table->index(['commentable_type', 'commentable_id', 'approved']);
        });

        Schema::create('ext_cyberpunk_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->morphs('likeable'); // posts, comentarios y productos
            $table->timestamps();

            $table->unique(['user_id', 'likeable_type', 'likeable_id'], 'cyberpunk_like_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_cyberpunk_likes');
        Schema::dropIfExists('ext_cyberpunk_comments');
    }
};
