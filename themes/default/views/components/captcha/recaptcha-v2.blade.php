<script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=captchaOnload" async defer></script>
<div id="g-recaptcha" data-sitekey="{{ config('settings.captcha_site_key') }}"></div>

<script>
    function captchaOnload() {
        // On livewire validation error reset captcha
        // Only register once, as the hook persists across wire:navigate page visits
        if (!window.captchaResetHookRegistered) {
            window.captchaResetHookRegistered = true;
            Livewire.hook('request', ({
                succeed,
                fail
            }) => {
                succeed(() => {
                    if (document.getElementById('g-recaptcha')) {
                        grecaptcha.reset();
                    }
                });
            })
        }

        grecaptcha.render('g-recaptcha', {
            sitekey: '{{ config('settings.captcha_site_key') }}',
            callback: function(token) {
                @this.set('captcha', token, false)
            },
        });
    }
</script>
