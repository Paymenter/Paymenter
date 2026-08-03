<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Support;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creación y reparación de las tablas de la extensión.
 *
 * Es la única fuente de verdad del esquema: las migraciones y el botón
 * "Reparar base de datos" del panel llaman aquí. Cada tabla se comprueba por
 * separado, así que si una instalación anterior quedó a medias (por ejemplo
 * con ext_cyberpunk_comments creada pero ext_cyberpunk_likes no), esto crea
 * sólo lo que falta sin fallar.
 */
class Database
{
    public const TABLES = [
        'ext_cyberpunk_posts',
        'ext_cyberpunk_post_media',
        'ext_cyberpunk_comments',
        'ext_cyberpunk_likes',
        'ext_cyberpunk_avatars',
        'ext_cyberpunk_visits',
    ];

    /**
     * Crea las tablas que falten. Devuelve las que ha creado.
     *
     * @return array<int, string>
     */
    public static function ensureTables(): array
    {
        $created = [];

        if (!Schema::hasTable('ext_cyberpunk_posts')) {
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

                $table->string('category', 32)->default('general');
                $table->index(['approved', 'pinned'], 'cyberpunk_posts_state_idx');
                $table->index('category', 'cyberpunk_posts_category_idx');
            });
            $created[] = 'ext_cyberpunk_posts';
        }

        // Instalaciones anteriores no tenían la columna de categoría.
        if (Schema::hasTable('ext_cyberpunk_posts') && !Schema::hasColumn('ext_cyberpunk_posts', 'category')) {
            Schema::table('ext_cyberpunk_posts', function (Blueprint $table) {
                $table->string('category', 32)->default('general');
                $table->index('category', 'cyberpunk_posts_category_idx');
            });
            $created[] = 'ext_cyberpunk_posts.category';
        }

        if (!Schema::hasTable('ext_cyberpunk_post_media')) {
            Schema::create('ext_cyberpunk_post_media', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('ext_cyberpunk_posts')->cascadeOnDelete();
                $table->string('type', 16)->default('image');
                $table->string('path');
                $table->unsignedInteger('sort')->default(0);
                $table->timestamps();
            });
            $created[] = 'ext_cyberpunk_post_media';
        }

        if (!Schema::hasTable('ext_cyberpunk_comments')) {
            Schema::create('ext_cyberpunk_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
                // 191 y no 255: con utf8mb4 un índice sobre 255 caracteres
                // supera el límite de bytes de MariaDB y la tabla no se crea.
                $table->string('commentable_type', 191);
                $table->unsignedBigInteger('commentable_id');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->text('content');
                $table->boolean('approved')->default(true);
                $table->unsignedInteger('likes_count')->default(0);
                $table->timestamps();

                // Reseñas: estrellas (1-5) y destacada en el inicio.
                $table->unsignedTinyInteger('rating')->nullable();
                $table->boolean('featured')->default(false);

                $table->index(['commentable_type', 'commentable_id'], 'cyberpunk_comments_target_idx');
                $table->index('parent_id', 'cyberpunk_comments_parent_idx');
                $table->index(['featured', 'rating'], 'cyberpunk_comments_featured_idx');
            });
            $created[] = 'ext_cyberpunk_comments';
        }

        // Instalaciones anteriores no tenían las estrellas.
        if (Schema::hasTable('ext_cyberpunk_comments') && !Schema::hasColumn('ext_cyberpunk_comments', 'rating')) {
            Schema::table('ext_cyberpunk_comments', function (Blueprint $table) {
                $table->unsignedTinyInteger('rating')->nullable();
            });
            $created[] = 'ext_cyberpunk_comments.rating';
        }

        if (Schema::hasTable('ext_cyberpunk_comments') && !Schema::hasColumn('ext_cyberpunk_comments', 'featured')) {
            Schema::table('ext_cyberpunk_comments', function (Blueprint $table) {
                $table->boolean('featured')->default(false);
                $table->index(['featured', 'rating'], 'cyberpunk_comments_featured_idx');
            });
            $created[] = 'ext_cyberpunk_comments.featured';
        }

        if (!Schema::hasTable('ext_cyberpunk_likes')) {
            Schema::create('ext_cyberpunk_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
                $table->string('likeable_type', 191);
                $table->unsignedBigInteger('likeable_id');
                $table->timestamps();

                $table->unique(['user_id', 'likeable_type', 'likeable_id'], 'cyberpunk_like_unique');
                $table->index(['likeable_type', 'likeable_id'], 'cyberpunk_likes_target_idx');
            });
            $created[] = 'ext_cyberpunk_likes';
        }

        if (!Schema::hasTable('ext_cyberpunk_avatars')) {
            Schema::create('ext_cyberpunk_avatars', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(User::class)->unique()->constrained()->cascadeOnDelete();
                $table->string('path');
                $table->timestamps();
            });
            $created[] = 'ext_cyberpunk_avatars';
        }

        if (!Schema::hasTable('ext_cyberpunk_visits')) {
            Schema::create('ext_cyberpunk_visits', function (Blueprint $table) {
                $table->id();
                $table->date('day')->unique();
                $table->unsignedBigInteger('count')->default(0);
                $table->timestamps();
            });
            $created[] = 'ext_cyberpunk_visits';
        }

        return $created;
    }

    /**
     * Tablas que siguen faltando.
     *
     * @return array<int, string>
     */
    public static function missingTables(): array
    {
        $missing = [];

        foreach (self::TABLES as $table) {
            try {
                if (!Schema::hasTable($table)) {
                    $missing[] = $table;
                }
            } catch (\Throwable $e) {
                $missing[] = $table;
            }
        }

        return $missing;
    }

    public static function isReady(): bool
    {
        return count(self::missingTables()) === 0;
    }

    public static function dropTables(): void
    {
        foreach (array_reverse(self::TABLES) as $table) {
            Schema::dropIfExists($table);
        }
    }
}
