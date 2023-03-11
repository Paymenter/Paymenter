<div class="hidden mt-3" id="tab-security">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.security') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2">
            <h2 class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('Recaptcha:') }}</h2>
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('recaptcha') is-invalid @enderror"
                    placeholder=" " name="recaptcha" value="1"
                    {{ config('settings::recaptcha') ? 'checked' : '' }} />
                <label class="form-label" style="position: unset;">{{ __('Enable Recaptcha') }}</label>
            </div>
            <div class="relative m-4 group">
                <select name="recaptcha_type" class="form-input peer">
                    <option value="v2" {{ config('settings::recaptcha_type') == 'v2' ? 'selected' : '' }}>
                        {{ __('Recaptcha v2 (checkbox)') }}</option>
                    <option value="v2_invisible"
                        {{ config('settings::recaptcha_type') == 'v2_invisible' ? 'selected' : '' }}>
                        {{ __('Recaptcha v2 (invisible)') }}</option>
                    <option value="v3" {{ config('settings::recaptcha_type') == 'v3' ? 'selected' : '' }}>
                        {{ __('Recaptcha v3') }}</option>
                    <option value="turnstile"
                        {{ config('settings::recaptcha_type') == 'turnstile' ? 'selected' : '' }}>
                        {{ __('Cloudflare Turnstile') }}</option>
                    <option value="hcaptcha" {{ config('settings::recaptcha_type') == 'hcaptcha' ? 'selected' : '' }}>
                        {{ __('hCaptcha') }}</option>
                </select>
                <label class="form-label">{{ __('Recaptcha type') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('recaptcha_site_key') is-invalid @enderror"
                    placeholder=" " name="recaptcha_site_key" value="{{ config('settings::recaptcha_site_key') }}" />
                <label class="form-label">{{ __('Recaptcha public key') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('recaptcha_secret_key') is-invalid @enderror"
                    placeholder=" " name="recaptcha_secret_key"
                    value="{{ config('settings::recaptcha_secret_key') }}" />
                <label class="form-label">{{ __('Recaptcha Secret key') }}</label>
            </div>
        </div>
        <button class="form-submit float-right">{{ __('Submit') }}</button>
    </form>
</div>
