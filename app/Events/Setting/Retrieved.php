<?php

namespace App\Events\Setting;

use App\Models\Setting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class Retrieved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(Setting $setting)
    {
        if ($setting->encrypted && $setting->value) {
            try {
                $setting->value = Crypt::decryptString($setting->value);
            } catch (Throwable $th) {
                // Normal `throw new Exception($th)` wasn't working here, so we are using dump-and-die for now.
                dd($th, $setting->value);
            }
        }

        switch ($setting->type) {
            case 'boolean':
                $setting->value = (bool) $setting->value;
            case 'integer':
                $setting->value = (int) $setting->value;
            case 'float':
                $setting->value = (float) $setting->value;
            case 'array':
                $setting->value = json_decode($setting->value, true);
            default:
                return;
        }
    }
}
