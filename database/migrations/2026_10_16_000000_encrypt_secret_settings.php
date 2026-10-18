<?php

use App\Models\Extension;
use App\Models\Gateway;
use App\Models\Server;
use App\Models\Setting;
use App\Redactors\RightRedactor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Secrets that were stored (and audited) in plaintext
    private const CORE = ['captcha_secret', 'oauth_google_client_secret', 'oauth_github_client_secret', 'oauth_discord_client_secret'];

    private const EXTENSIONS = [
        'CPanel' => ['apikey'],
        'Convoy' => ['api_key'],
        'DirectAdmin' => ['password'],
        'Enhance' => ['apikey'],
        'Plesk' => ['password'],
        'Pterodactyl' => ['api_key'],
        'Virtfusion' => ['apikey'],
        'Virtualizor' => ['key', 'password'],
        'Mollie' => ['api_key'],
        'PayPal' => ['client_secret'],
        'Stripe' => ['stripe_secret_key', 'stripe_webhook_secret'],
        'DiscordNotifications' => ['webhook_url'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $ids = DB::table('settings')->whereNull('settingable_type')->whereIn('key', self::CORE)->pluck('id');

        foreach (self::EXTENSIONS as $extension => $keys) {
            $ids = $ids->merge(DB::table('settings')
                ->join('extensions', 'extensions.id', '=', 'settings.settingable_id')
                ->whereIn('settings.settingable_type', [Extension::class, Gateway::class, Server::class])
                ->where('extensions.extension', $extension)
                ->whereIn('settings.key', $keys)
                ->pluck('settings.id'));
        }

        // Query builder on purpose: the Setting model events would encrypt again and write new audits
        DB::table('settings')->whereIn('id', $ids)->where('encrypted', false)->whereNotNull('value')->chunkById(100, function ($settings) {
            foreach ($settings as $setting) {
                DB::table('settings')->where('id', $setting->id)->update([
                    'value' => Crypt::encryptString($setting->value),
                    'encrypted' => true,
                ]);
            }
        });

        // Redact the values already written to the audit log, the same way new audits of encrypted settings are
        DB::table('audits')->where('auditable_type', Setting::class)->whereIn('auditable_id', $ids)->chunkById(100, function ($audits) {
            foreach ($audits as $audit) {
                $values = [];
                foreach (['old_values', 'new_values'] as $column) {
                    $data = json_decode($audit->$column ?? '', true);
                    if (is_array($data) && isset($data['value'])) {
                        $data['value'] = RightRedactor::redact((string) $data['value']);
                        $values[$column] = json_encode($data);
                    }
                }
                if ($values) {
                    DB::table('audits')->where('id', $audit->id)->update($values);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Encrypted values keep working without this migration, and redacted audits cannot be restored
    }
};
