<div class="hidden mt-3" id="tab-general">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.general') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('app_name') is-invalid @enderror" placeholder=" "
                    name="app_name" required value="{{ config('settings::app_name') }}" />
                <label class="form-label">{{ __('Name') }}</label>
            </div>
            <!-- App Logo -->
            <div class="relative m-4 group">
                <input type="file" class="form-input peer @error('app_logo') is-invalid @enderror" placeholder=" "
                    name="app_logo" accept="image/*" />
                <label class="form-label">{{ __('Logo') }}</label>
            </div>
            <div class="relative m-4 group">
                <select name="theme" class="form-input peer @error('theme') is-invalid @enderror" placeholder=" "
                    name="theme" required>
                    @foreach ($themes as $theme)
                        <option value="{{ $theme }}" {{ $theme == config('settings::theme') ? 'selected' : '' }}>
                            {{ $theme }}</option>
                    @endforeach
                </select>
                <label class="form-label">{{ __('Theme') }}</label>
            </div>
            <div class="relative m-4 group">
                <!-- sidebar -->
                <select name="sidebar" class="form-input peer @error('sidebar') is-invalid @enderror" placeholder=" "
                    name="sidebar" required>
                    <option value="0" {{ config('settings::sidebar') == 0 ? 'selected' : '' }}>
                        {{ __('Topbar') }}</option>
                    <option value="1" {{ config('settings::sidebar') == 1 ? 'selected' : '' }}>
                        {{ __('Sidebar') }}</option>
                </select>
            </div>
            <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
            <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
            <div class="relative m-4 group md:col-span-2 col-span-1">
                <textarea name="home_page_text" class="form-input peer @error('home_page_text') is-invalid @enderror" placeholder=" "
                    name="home_page_text" id="home_page_text"></textarea>
            </div>
            <script>
                var easyMDE = new EasyMDE({
                    element: document.getElementById("home_page_text"),
                });
                easyMDE.value(`{{ config('settings::home_page_text') }}`);
            </script>

            <h2 class="col-span-1 md:col-span-2 text-xl text-gray-900 dark:text-darkmodetext ">SEO: </h2>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('seo_title') is-invalid @enderror" placeholder=" "
                    name="seo_title" required value="{{ config('settings::seo_title') }}" />
                <label class="form-label">{{ __('Seo Title') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('seo_description') is-invalid @enderror"
                    placeholder=" " name="seo_description" required
                    value="{{ config('settings::seo_description') }}" />
                <label class="form-label">{{ __('Seo Description') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('seo_keywords') is-invalid @enderror"
                    placeholder=" " name="seo_keywords" required value="{{ config('settings::seo_keywords') }}" />
                <label class="form-label">{{ __('Seo Keywords (separate with comma)') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('seo_twitter_card') is-invalid @enderror"
                    placeholder=" " name="seo_twitter_card" value="1"
                    {{ config('settings::seo_twitter_card') ? 'checked' : '' }} />
                <label class="form-label" style="position: unset;">{{ __('Seo Twitter Card') }}</label>
            </div>
            <div class="relative m-4 group">
                <select name="snow" class="form-input peer @error('snow') is-invalid @enderror" placeholder=" "
                    name="snow" required>
                    <option value="1" {{ config('settings::snow') == 1 ? 'selected' : '' }}>
                        {{ __('Enabled') }}</option>
                    <option value="0" {{ config('settings::snow') == 0 ? 'selected' : '' }}>
                        {{ __('Disabled') }}</option>
                </select>
                <label class="form-label">{{ __('Snow') }}</label>
            </div>
        </div>
        <button class="float-right form-submit">{{ __('Submit') }}</button>
    </form>
</div>
