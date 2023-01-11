<div class="hidden mt-3" id="tab-security">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.security') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- discord -->
            <h2 class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('Google Recaptcha:')}}</h2>
            <!-- enable discord -->
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('seo_twitter_card') is-invalid @enderror"
                    placeholder=" " name="recaptcha" value="1"
                    {{ config('settings::recaptcha') ? 'checked' : '' }} />
                <label class="form-label" style="position: unset;">{{ __('Enable Recaptcha') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('recaptcha_site_key') is-invalid @enderror" placeholder=" "
                    name="recaptcha_site_key" value="{{ config('settings::recaptcha_site_key') }}" />
                <label class="form-label">{{ __('Recaptcha public key') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('recaptcha_secret_key') is-invalid @enderror"
                    placeholder=" " name="recaptcha_secret_key" value="{{ config('settings::recaptcha_secret_key') }}" />
                <label class="form-label">{{ __('Recaptcha Secret key') }}</label>
            </div>
        </div>
        <button class="form-submit float-right">{{ __('Submit') }}</button>
    </form>
</div>

