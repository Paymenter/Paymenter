<script src="https://www.google.com/recaptcha/api.js?render={{ config('settings.captcha_site_key') }}" async defer>
</script>
<div id="g-recaptcha" data-sitekey="{{ config('settings.captcha_site_key') }}"></div>

<script>
    document.addEventListener('livewire:initialized', () => {
        const submitBtn = document.querySelector('#{{ $form }} button[type="submit"]')

        submitBtn.addEventListener("click", function(event) {
            event.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('settings.captcha_site_key') }}', {
                    action: 'submit'
                }).then(function(token) {
                    @this.set('captcha', token, false)
                    @this.submit();
                });
            });
        });
    });
</script>
