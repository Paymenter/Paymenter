<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Crypt;

trait Encrypted
{
    // Listen for get and set events
    public static function bootEncrypted(): void
    {
        static::saving(function ($model) {
            foreach ($model->getAttributes() as $key => $value) {
                if ($model->isEncrypted($key)) {
                    try {
                        $model->setAttribute($key, Crypt::encryptString($value));
                    } catch (\Exception $e) {
                        $model->setAttribute($key, $value);
                    }
                }
            }
        });

        static::retrieved(function ($model) {
            foreach ($model->getAttributes() as $key => $value) {
                if ($model->isEncrypted($key)) {
                    try {
                        $model->setAttribute($key, Crypt::decryptString($value));
                    } catch (\Exception $e) {
                        $model->setAttribute($key, $value);
                    }
                }
            }
        });
    }

    public function isEncrypted(string $key): bool
    {
        return $this->encrypted && $this->isFillable($key);
    }
}
