<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait Recaptcha
{
    public string $recaptcha;

    private $is_valid = false;

    public function updated()
    {
        $this->recaptcha();
    }

    // Recaptcha
    private function recaptcha()
    {
        if (!config('settings.recaptcha') || config('settings.recaptcha') == 'disabled' || $this->is_valid) {
            return;
        }

        if (config('settings.recaptcha') == 'turnstile') {
            $this->turnstile($this->recaptcha);
        } elseif (config('settings.recaptcha') == 'hcaptcha') {
            $this->hcaptcha($this->recaptcha);
        } else {
            $this->google($this->recaptcha);
        }
    }

    // Turnstile
    private function turnstile($value)
    {
        $itempotencyKey = uniqid();

        $response = Http::asForm()->acceptJson()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('settings.recaptcha_secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
            'idempotency_key' => $itempotencyKey,
        ]);

        if ($response->json()['success']) {
            return $this->is_valid = true;
        }

        $subResponse = Http::asForm()->acceptJson()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('settings.recaptcha_secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
            'idempotency_key' => $itempotencyKey,
        ]);

        if ($subResponse->json()['success']) {
            return $this->is_valid = true;
        }

        Log::error('The reCAPTCHA was invalid.' . $value, $response->json(), $subResponse->json());
        throw ValidationException::withMessages(['recaptcha' => 'The reCAPTCHA was invalid.']);

        return $this->is_valid = true;
    }
}