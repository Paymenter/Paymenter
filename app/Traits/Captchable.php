<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait Captchable
{
    public string $captcha = '';

    private $is_valid = false;

    public function updated()
    {
        $this->captcha();
    }

    // Captchable
    private function captcha()
    {
        if (!config('settings.captcha') || config('settings.captcha') == 'disabled' || $this->is_valid) {
            return;
        }

        if (!$this->captcha) {
            throw ValidationException::withMessages(['captcha' => 'The CAPTCHA is required.']);
        }

        if (config('settings.captcha') == 'turnstile') {
            $this->turnstile($this->captcha);
        } elseif (config('settings.captcha') == 'hcaptcha') {
            $this->hcaptcha($this->captcha);
        } elseif (config('settings.captcha') == 'recaptcha-v2' || config('settings.captcha') == 'recaptcha-v3') {
            $this->recaptcha($this->captcha);
        }
    }

    // Turnstile
    private function turnstile($value)
    {
        $itempotencyKey = uniqid();

        $response = Http::asForm()->acceptJson()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('settings.captcha_secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
            'idempotency_key' => $itempotencyKey,
        ]);

        if ($response->json()['success']) {
            return $this->is_valid = true;
        }

        $subResponse = Http::asForm()->acceptJson()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('settings.captcha_secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
            'idempotency_key' => $itempotencyKey,
        ]);

        if ($subResponse->json()['success']) {
            return $this->is_valid = true;
        }

        Log::error('The CAPTCHA was invalid.' . $value, $response->json(), $subResponse->json());
        throw ValidationException::withMessages(['captcha' => 'The CAPTCHA was invalid.']);

        return $this->is_valid = true;
    }

    // Google Recaptcha
    private function recaptcha($value)
    {
        $response = Http::asForm()->acceptJson()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('settings.captcha_secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if ($response->json()['success']) {
            return $this->is_valid = true;
        }

        dd($response->json());

        Log::error('The CAPTCHA was invalid.' . $value, $response->json());
        throw ValidationException::withMessages(['captcha' => 'The CAPTCHA was invalid.']);

        return $this->is_valid = true;
    }
}
