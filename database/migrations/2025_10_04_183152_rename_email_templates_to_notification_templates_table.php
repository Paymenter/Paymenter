<?php

use Database\Seeders\EmailTemplateSeeder;
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
        Schema::rename('email_templates', 'notification_templates');

        Schema::table('notification_templates', function (Blueprint $table) {
            $table->enum('mail_enabled', ['force', 'choice_on', 'choice_off', 'never'])->default('choice_on')->after('enabled');
            $table->enum('in_app_enabled', ['force', 'choice_on', 'choice_off', 'never'])->default('choice_on')->after('mail_enabled');
            $table->string('in_app_title')->nullable()->after('subject');
            $table->text('in_app_body')->nullable()->after('body');
            $table->string('in_app_url')->nullable()->after('in_app_body');
            // String to display to user when editing notification preferences
            $table->string('edit_preference_message')->nullable()->after('in_app_body');
        });

        // Update existing records to set in_app_title and in_app_body to default values
        foreach (EmailTemplateSeeder::mapping as $key => $data) {
            if (empty($data['in_app_title']) || empty($data['in_app_body'])) {
                continue;
            }
            DB::table('notification_templates')
                ->where('key', $key)
                ->update([
                    'in_app_title' => $data['in_app_title'],
                    'in_app_body' => $data['in_app_body'],
                    'mail_enabled' => $data['mail_enabled'],
                    'in_app_enabled' => $data['in_app_enabled'],
                    'edit_preference_message' => $data['edit_preference_message'] ?? null,
                    'in_app_url' => $data['in_app_url'] ?? null,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->dropColumn(['mail_enabled', 'in_app_enabled', 'in_app_title', 'in_app_body', 'edit_preference_message', 'in_app_url']);
        });

        Schema::rename('notification_templates', 'email_templates');
    }
};
