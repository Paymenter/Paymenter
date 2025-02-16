@if (config('settings.captcha') !== 'disabled')
    <div class="flex flex-col justify-center mt-2">
        <div wire:ignore>
            @if (config('settings.captcha') == 'recaptcha-v2')
                <x-captcha.recaptcha-v2 :$form />
            @elseif(config('settings.captcha') == 'recaptcha-v3')
                <x-captcha.recaptcha-v3 :$form />
            @elseif(config('settings.captcha') == 'turnstile')
                <x-captcha.turnstile :$form />
            @elseif(config('settings.captcha') == 'hcaptcha')
                <x-captcha.hcaptcha :$form />
            @endif
        </div>
        @error('captcha')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
@endif
