<div class="hidden mt-3" id="tab-login">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.login') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- discord -->
            <h2 class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('Discord:')}}</h2>
            <!-- enable discord -->
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('seo_twitter_card') is-invalid @enderror"
                    placeholder=" " name="discord_enabled" value="1"
                    {{ config('settings::discord_enabled') ? 'checked' : '' }} />
                <label class="form-label" style="position: unset;">{{ __('Enable Discord') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('discord_client_id') is-invalid @enderror" placeholder=" "
                    name="discord_client_id" value="{{ config('services.discord.client_id') }}" />
                <label class="form-label">{{ __('Discord Client ID') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('discord_client_secret') is-invalid @enderror"
                    placeholder=" " name="discord_client_secret" value="{{ config('services.discord.client_secret') }}" />
                <label class="form-label">{{ __('Discord Client Secret') }}</label>
            </div>
        </div>
        <button class="form-submit float-right">{{ __('Submit') }}</button>
    </form>
</div>

