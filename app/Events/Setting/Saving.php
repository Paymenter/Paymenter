<?php

namespace App\Events\Setting;

use App\Models\Setting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class Saving
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(Setting $setting)
    {
        if ($setting->encrypted) {
            try {
                $setting->value = Crypt::encryptString($setting->value);
            } catch (Throwable $th) {
                // Normal `throw new Exception($th)` wasn't working here, so we are using dump-and-die for now.
                dd($th, $setting->value);
            }

            // An encrypted value can only be a string, so we refrain from converting its type
            return $setting;
        }

        switch ($setting->type) {
            case 'boolean':
                $setting->value = (bool) $setting->value;
            case 'integer':
                $setting->value = (int) $setting->value;
            case 'float':
                $setting->value = (float) $setting->value;
            case 'array':
                if (!is_string($setting->value) || is_null(json_decode($setting->value))) {
                    $setting->value = json_encode($setting->value);
                }
                break;
            default:
                return $setting;
        }
    }
}
