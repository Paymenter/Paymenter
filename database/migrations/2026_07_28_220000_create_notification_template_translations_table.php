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
        Schema::create('notification_template_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_template_id');
            $table->string('locale', 16);
            $table->string('subject');
            $table->text('body');
            $table->string('in_app_title')->nullable();
            $table->text('in_app_body')->nullable();
            $table->string('in_app_url')->nullable();
            $table->string('edit_preference_message')->nullable();
            $table->timestamps();

            $table->foreign('notification_template_id', 'ntt_template_id_foreign')
                ->references('id')
                ->on('notification_templates')
                ->cascadeOnDelete();
            $table->unique(['notification_template_id', 'locale'], 'notification_template_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_template_translations');
    }
};
