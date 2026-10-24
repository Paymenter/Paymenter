<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * DirectAdmin account passwords were stored in plain text in the service properties.
     * Encrypt them; values that are already encrypted are left untouched.
     */
    public function up(): void
    {
        DB::table('properties')->where('key', 'directadmin_password')->where('value', '!=', '')->orderBy('id')
            ->chunkById(500, function ($properties) {
                foreach ($properties as $property) {
                    try {
                        Crypt::decryptString($property->value);
                    } catch (DecryptException) {
                        DB::table('properties')->where('id', $property->id)->update(['value' => Crypt::encryptString($property->value)]);
                    }
                }
            });
    }

    public function down(): void
    {
        DB::table('properties')->where('key', 'directadmin_password')->where('value', '!=', '')->orderBy('id')
            ->chunkById(500, function ($properties) {
                foreach ($properties as $property) {
                    try {
                        DB::table('properties')->where('id', $property->id)->update(['value' => Crypt::decryptString($property->value)]);
                    } catch (DecryptException) {
                        // Already plain text
                    }
                }
            });
    }
};
