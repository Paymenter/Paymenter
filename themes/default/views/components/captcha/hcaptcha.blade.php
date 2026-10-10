<script src="https://js.hcaptcha.com/1/api.js?render=explicit&onload=captchaOnload" async defer></script>
<div id="h-captcha" data-sitekey="{{ config('settings.captcha_site_key') }}"></div>

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
                    if (document.getElementById('h-captcha')) {
                        hcaptcha.reset();
                    }
                });
            })
        }

        hcaptcha.render('h-captcha', {
            sitekey: '{{ config('settings.captcha_site_key') }}',
            callback: function(token) {
                @this.set('captcha', token, false)
            },
        });
    }
</script>
