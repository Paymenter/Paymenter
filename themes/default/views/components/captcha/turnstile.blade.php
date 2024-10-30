<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit"></script>

<div id="cf-turnstile"></div>

<script>
    document.addEventListener('livewire:init', () => {
        function renderTurnstile() {

            const isDarkMode = document.documentElement.classList.contains('dark');
            const theme = isDarkMode ? 'dark' : 'light';

            turnstile.render('#cf-turnstile', {
                sitekey: '{{ config('settings.captcha_site_key') }}',
                size: 'flexible',
                theme: theme,
                callback: function(token) {
                    @this.set('captcha', token, false);
                }
            });
        }

        renderTurnstile();

        // On livewire validation error reset turnstile
        Livewire.hook('morph.updated', () => {
            renderTurnstile();
        });
    });
</script>
