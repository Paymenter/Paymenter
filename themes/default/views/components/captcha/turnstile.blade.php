@assets
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit"></script>
@endassets

<div data-captcha-turnstile></div>

@script
<script>
    const turnstileElement = $wire.$el.querySelector('[data-captcha-turnstile]');
    let turnstileWidgetId;
    const componentId = $wire.$id;

    function renderTurnstile() {
        const isDarkMode = localStorage.getItem('_x_darkMode') === 'true';
        const theme = isDarkMode ? 'dark' : 'light';

        turnstileWidgetId = turnstile.render(turnstileElement, {
            sitekey: '{{ config('settings.captcha_site_key') }}',
            size: 'flexible',
            theme: theme,
            callback: token => $wire.set('captcha', token, false),
            'expired-callback': () => $wire.set('captcha', '', false),
            'error-callback': () => $wire.set('captcha', '', false),
        });
    }

    Livewire.hook('commit', ({ component, succeed }) => {
        if (component.id !== componentId) {
            return;
        }

        succeed(() => {
            $wire.set('captcha', '', false);
            turnstile.reset(turnstileWidgetId);
        });
    });

    renderTurnstile();
</script>
@endscript
