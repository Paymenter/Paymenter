@if (config('settings::recaptcha') == 1)
    @if (config('settings::recaptcha_type') == 'v2' || !config('settings::recaptcha_type'))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <div class="g-recaptcha" data-sitekey="{{ config('settings::recaptcha_site_key') }}"></div>
    @elseif(config('settings::recaptcha_type') == 'v2_invisible' || config('settings::recaptcha_type') == 'v3')
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('settings::recaptcha_site_key') }}" async defer></script>
        <div class="g-recaptcha" data-sitekey="{{ config('settings::recaptcha_site_key') }}" data-callback="onSubmit"
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
    @elseif(config('settings::recaptcha_type') == 'turnstile')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        <div class="cf-turnstile" data-sitekey="{{ config('settings::recaptcha_site_key') }}"></div>
    @elseif(config('settings::recaptcha_type') == 'hcaptcha')
        <script src="https://hcaptcha.com/1/api.js" async defer></script>
        <div class="h-captcha" data-sitekey="{{ config('settings::recaptcha_site_key') }}"></div>
    @endif
@endif
