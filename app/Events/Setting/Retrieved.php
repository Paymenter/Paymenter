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
                report($th);
                $setting->value = null;
            }
        }

        switch ($setting->type) {
            case 'boolean':
                $setting->value = (bool) $setting->value;
                break;
            case 'integer':
                $setting->value = (int) $setting->value;
                break;
            case 'float':
                $setting->value = (float) $setting->value;
                break;
            case 'array':
                $setting->value = json_decode($setting->value, true);
                break;
            default:
                return;
        }
    }
}
