<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_kb_categories', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('sort_order');
        });

        Schema::table('ext_kb_articles', function (Blueprint $table) {
            $table->index(['category_id', 'is_active']);
            $table->index('published_at');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('ext_kb_articles', function (Blueprint $table) {
            $table->dropIndex('ext_kb_articles_category_id_is_active_index');
            $table->dropIndex('ext_kb_articles_published_at_index');
            $table->dropIndex('ext_kb_articles_sort_order_index');
        });

        Schema::table('ext_kb_categories', function (Blueprint $table) {
            $table->dropIndex('ext_kb_categories_is_active_index');
            $table->dropIndex('ext_kb_categories_sort_order_index');
        });
    }
};
