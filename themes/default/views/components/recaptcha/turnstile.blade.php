<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit"></script>

<div id="cf-turnstile"></div>

<script>
    document.addEventListener('livewire:init', () => {
        // On livewire validation error reset turnstile
        Livewire.hook('morph.updated', () => {
            turnstile.render('#cf-turnstile', {
                sitekey: '{{ config('settings.recaptcha_site_key') }}',
                callback: function(token) {
                    @this.set('recaptcha', token, false)
                },
            });
        });

        turnstile.render('#cf-turnstile', {
            sitekey: '{{ config('settings.recaptcha_site_key') }}',
            callback: function(token) {
                @this.set('recaptcha', token, false)
            },
        });
    });
</script>
