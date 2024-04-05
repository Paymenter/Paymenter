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
                <input type="file" class="form-input p-0 peer @error('app_logo') is-invalid @enderror"
                       placeholder=" " name="app_logo" accept="image/*" />
                <label class="form-label">{{ __('Logo') }}</label>
            </div>
            <div class="relative m-4 group">
                <!-- TImezone -->
                <select name="timezone" class="form-input peer @error('timezone') is-invalid @enderror" placeholder=" "
                        name="timezone" required>
                    @foreach ($timezones as $timezone)
                        <option value="{{ $timezone }}" {{ $timezone == config('settings::timezone') ? 'selected' : '' }}>
                            {{ $timezone }}</option>
                    @endforeach
                </select>
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
                <label class="form-label">{{ __('Menu display style') }}</label>
            </div>
            <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
            <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
            <div class="relative m-4 group md:col-span-2 col-span-1">
                <label class="dark:text-darkmodetext block text-md font-medium text-gray-700" for="home_page_text">
                    {{ __('Home Page Text') }}
                </label>
                <textarea
                    name="home_page_text"
                    class="form-input w-full @error('home_page_text') is-invalid @enderror"
                    placeholder=" "
                    name="home_page_text"
                    id="home_page_text"
                >{{ config('settings::home_page_text') }}</textarea>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var easyMDE = new EasyMDE({
                        element: document.getElementById("home_page_text"),
                        spellChecker: false,
                        toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|", "link", "image", "table", "|", "preview", "side-by-side", "fullscreen", "|", "guide"]
                    });
                });
            </script>
            <h2 class="col-span-1 md:col-span-2 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('Orders') }}</h2>
            <div class="relative m-4 group">
                <input type="number" class="form-input peer @error('remove_unpaid_order_after') is-invalid @enderror"
                       placeholder=" " name="remove_unpaid_order_after" value="{{ config('settings::remove_unpaid_order_after') }}" />
                <label class="form-label">{{ __('Days after which an unpaid server will remove itself') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="time" class="form-input peer @error('run_cronjob_at') is-invalid @enderror"
                       placeholder=" " name="run_cronjob_at" value="{{ config('settings::run_cronjob_at') }}" />
                <label class="form-label">{{ __('Run cronjob at') }}</label>
            </div>
            <h2 class="col-span-1 md:col-span-2 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('Currency') }}
            </h2>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('currency_sign') is-invalid @enderror"
                       placeholder=" " name="currency_sign" value="{{ config('settings::currency_sign') }}" />
                <label class="form-label">{{ __('Currency Sign') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('currency') is-invalid @enderror" placeholder=" "
                       name="currency" value="{{ config('settings::currency') }}" />
                <label class="form-label">{{ __('Currency Code') }}</label>
            </div>
            <div class="relative m-4 group">
                <select name="currency_position" class="form-input peer @error('currency_position') is-invalid @enderror"
                        placeholder=" " name="currency_position" required>
                    <option value="left" {{ config('settings::currency_position') == 'left' ? 'selected' : '' }}>
                        {{ __('Left') }}</option>
                    <option value="right" {{ config('settings::currency_position') == 'right' ? 'selected' : '' }}>
                        {{ __('Right') }}</option>
                </select>
                <label class="form-label">{{ __('Currency Position') }}</label>
            </div>
            <h2 class="col-span-1 md:col-span-2 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('Language') }}
            </h2>
            <div class="relative m-4 group">
                <select class="form-input peer @error('language') is-invalid @enderror" placeholder=" "
                        name="language" required>
                    @foreach ($languages as $language)
                        <option value="{{ $language }}" {{ $language == config('settings::language') ? 'selected' : '' }}>
                            {{ $language }}</option>
                    @endforeach
                </select>
            </div>
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('allow_auto_lang') is-invalid @enderror"
                       placeholder=" " name="allow_auto_lang" value="1"
                       {{ config('settings::allow_auto_lang') ? 'checked' : '' }} data-popover-target="language"/>
                <label class="form-label" style="position: unset;"  >{{ __('Allow Auto Language') }}</label>
                <div id="language" role="tooltip" data-popover
                     class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    {{ __('If enabled, the language will be automatically set to the language of the browser.') }}
                    <div data-popper-arrow></div>
                </div>
            </div>
            <h2 class="col-span-1 md:col-span-2 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('SEO') }}</h2>
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
                       placeholder=" " name="seo_keywords" value="{{ config('settings::seo_keywords') }}" />
                <label class="form-label">{{ __('Seo Keywords (separate with comma)') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('seo_twitter_card') is-invalid @enderror"
                       placeholder=" " name="seo_twitter_card" value="1"
                    {{ config('settings::seo_twitter_card') ? 'checked' : '' }} />
                <label class="form-label" style="position: unset;">{{ __('Seo Twitter Card') }}</label>
            </div>
        </div>
        <button class="float-right form-submit">{{ __('Submit') }}</button>
    </form>
</div>
