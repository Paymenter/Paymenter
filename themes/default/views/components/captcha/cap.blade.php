@php($capServerUrl = rtrim((string) config('settings.captcha_url'), '/'))

<div data-captcha-cap class="w-full flex flex-col items-center">
    <p data-captcha-cap-error class="hidden text-red-500 text-xs mt-1"></p>
</div>

@script
<script>
    const capContainer = $wire.$el.querySelector('[data-captcha-cap]');
    const capError = capContainer.querySelector('[data-captcha-cap-error]');
    const capComponentId = $wire.$id;
    const capServerUrl = @js($capServerUrl);
    const capEndpoint = capServerUrl + '/' + @js((string) config('settings.captcha_site_key')) + '/';
    let capElement = null;

    function showCapError(message) {
        console.error('[Cap] ' + message);
        capError.textContent = message;
        capError.classList.remove('hidden');
    }

    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = () => reject(new Error('Failed to load ' + src));
            document.head.appendChild(script);
        });
    }

    async function ensureCapWidget() {
        if (window.customElements.get('cap-widget')) {
            return;
        }

        if (!capServerUrl) {
            throw new Error('Cap Server URL is not configured.');
        }

        try {
            await loadScript(capServerUrl + '/assets/widget.js');
        } catch (e) {
            // Fallback: public CDN
            await loadScript('https://cdn.jsdelivr.net/npm/@cap.js/widget');
        }

        await Promise.race([
            window.customElements.whenDefined('cap-widget'),
            new Promise((_, reject) => setTimeout(() => reject(new Error('cap-widget element was not registered.')), 5000)),
        ]);
    }

    function createCapWidget() {
        capElement = document.createElement('cap-widget');
        capElement.setAttribute('data-cap-api-endpoint', capEndpoint);
        capElement.addEventListener('solve', event => $wire.set('captcha', event.detail.token, false));
        capElement.addEventListener('reset', () => $wire.set('captcha', '', false));
        capElement.addEventListener('error', () => {
            $wire.set('captcha', '', false);
            showCapError('Cap challenge failed. Check Site Key and Server URL.');
        });
        capContainer.appendChild(capElement);
    }

    ensureCapWidget().then(createCapWidget).catch(e => showCapError(e.message));

    // Tokens are single-use: reset the widget after every Livewire request
    Livewire.hook('commit', ({ component, succeed }) => {
        if (component.id !== capComponentId) {
            return;
        }

        succeed(() => {
            $wire.set('captcha', '', false);
            if (capElement && typeof capElement.reset === 'function') {
                capElement.reset();
            }
        });
    });
</script>
@endscript
