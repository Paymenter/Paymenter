@if (config('settings.recaptcha') !== 'disabled')
    @if (config('settings.recaptcha') == 'v2')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="g-recaptcha" data-sitekey="{{ config('settings.recaptcha_site_key') }}"></div>
    @elseif(config('settings.recaptcha') == 'v2_invisible' || config('settings.recaptcha_type') == 'v3')
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('settings.recaptcha_site_key') }}" async defer></script>
        <div class="g-recaptcha" data-sitekey="{{ config('settings.recaptcha_site_key') }}" data-callback="onSubmit"
            data-size="invisible"></div>
        <script>
            function onSubmit(token) {
                document.getElementById('{{ $form }}').submit();
            }
            document.addEventListener("DOMContentLoaded", function(event) {
                document.getElementById('{{ $form }}').addEventListener("submit", function(event) {
                    event.preventDefault();
                    grecaptcha.execute();
                });
            });
        </script>
    @elseif(config('settings.recaptcha') == 'turnstile')
        <x-recaptcha.turnstile :$form />
    @elseif(config('settings.recaptcha') == 'hcaptcha')
        <script src="https://hcaptcha.com/1/api.js" async defer></script>
        <div class="h-captcha" data-sitekey="{{ config('settings.recaptcha_site_key') }}"></div>
    @endif
    @error('recaptcha')
        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
    @enderror
@endif