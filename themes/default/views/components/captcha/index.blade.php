@if (config('settings.captcha') !== 'disabled')
    <div class="flex flex-col items-center justify-center mt-2">
        @if (config('settings.captcha') == 'google-v2')
            <x-captcha.recaptcha-v2 :$form />
        @elseif(config('settings.captcha') == 'google-v3')
            <script src="https://www.google.com/recaptcha/api.js?render={{ config('settings.captcha_site_key') }}" async defer>
            </script>
            <div class="g-recaptcha" data-sitekey="{{ config('settings.captcha_site_key') }}" data-callback="onSubmit"
                data-size="invisible"></div>
            <script>
                function onSubmit(token) {
                    document.getElementById('{{ $form }}').submit();
                }
                document.addEventListener("DOMContentLoaded", function(event) {
                    document.getElementById('{{ $form }}').addEventListener("submit", function(event) {
                        event.preventDefault();
                        gcaptcha.execute();
                    });
                });
            </script>
        @elseif(config('settings.captcha') == 'turnstile')
            <x-captcha.turnstile :$form />
        @elseif(config('settings.captcha') == 'hcaptcha')
            <script src="https://hcaptcha.com/1/api.js" async defer></script>
            <div class="h-captcha" data-sitekey="{{ config('settings.captcha_site_key') }}"></div>
        @endif
        @error('captcha')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
@endif
