<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Redact password hashes and 2FA secrets in existing user audits, and drop
     * remember tokens.
     */
    public function up(): void
    {
        DB::table('audits')
            ->where('auditable_type', User::class)
            ->chunkById(500, function ($audits) {
                foreach ($audits as $audit) {
                    $old = $this->clean($audit->old_values);
                    $new = $this->clean($audit->new_values);

                    if ($old === null && $new === null) {
                        continue;
                    }

                    $old ??= json_decode($audit->old_values ?? '[]', true) ?: [];
                    $new ??= json_decode($audit->new_values ?? '[]', true) ?: [];

                    // Only remember_token changed: nothing left worth keeping
                    if (empty($old) && empty($new)) {
                        DB::table('audits')->where('id', $audit->id)->delete();

                        continue;
                    }

                    DB::table('audits')->where('id', $audit->id)->update([
                        'old_values' => json_encode($old),
                        'new_values' => json_encode($new),
                    ]);
                }
            });
    }

    /**
     * Returns the cleaned values, or null when nothing needed changing.
     */
    private function clean(?string $json): ?array
    {
        $values = json_decode($json ?? '[]', true);

        if (!is_array($values)) {
            return null;
        }

        $changed = false;

        foreach (['password', 'tfa_secret'] as $key) {
            if (isset($values[$key]) && $values[$key] !== '' && $values[$key] !== '[redacted]') {
                $values[$key] = '[redacted]';
                $changed = true;
            }
        }

        if (array_key_exists('remember_token', $values)) {
            unset($values['remember_token']);
            $changed = true;
        }

        return $changed ? $values : null;
    }

    /**
     * The original values cannot be restored.
     */
    public function down(): void {}
};
