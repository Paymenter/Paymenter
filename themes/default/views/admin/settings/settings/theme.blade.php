<div class="hidden mt-3" id="tab-theme">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.theme') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2">

            <div class="relative m-4 group">
                <select name="theme" class="form-input peer @error('theme') is-invalid @enderror" placeholder=" "
                    name="theme" required>
                    @foreach ($themes as $theme)
                        <option value="{{ $theme }}" {{ $theme == config('settings::theme-active') ? 'selected' : '' }}>
                            {{ $theme }}</option>
                    @endforeach
                </select>
                <label class="form-label">{{ __('Theme') }}</label>
            </div>
            @foreach ($themeConfig->settings as $setting)
                @if ($setting->type == 'subcategory')

                    <h2 class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext md:col-span-2">{{ __($setting->label) }}</h2>
                    @foreach ($setting->settings as $subSetting)
                        <div class="relative m-4 group">
                            @if ($subSetting->type == 'color')
                                <x-input :name="$subSetting->name" :type="$subSetting->type" :value="config('settings::theme:' . $subSetting->name, $subSetting->default ?? null)" :label="$subSetting->label"
                                    :id="$subSetting->name" class="w-fit" />
                            @elseif($subSetting->type == 'checkbox')
                                <input type="hidden" name="{{ $subSetting->name }}" value="0">
                                <x-input :name="$subSetting->name" :type="$subSetting->type" :value="1" :label="$subSetting->label"
                                    :checked="config('settings::theme:' . $subSetting->name, $subSetting->default ?? false) == 1" :id="$subSetting->name" />
                            @else
                                <x-input :name="$subSetting->name" :type="$subSetting->type" :value="config('settings::theme:' . $subSetting->name, $subSetting->default ?? null)" :label="$subSetting->label"
                                    :id="$subSetting->name" />
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="relative m-4 group">
                        @if ($setting->type == 'color')
                            <x-input :name="$setting->name" :type="$setting->type" :value="config('settings::theme:' . $setting->name, $setting->name)" :label="$setting->label"
                                :id="$setting->name" class="w-fit" />
                        @elseif($setting->type == 'checkbox')
                            <input type="hidden" name="{{ $setting->name }}" value="0">
                            <x-input :name="$setting->name" :type="$setting->type" :value="1" :label="$setting->label"
                                :checked="config('settings::theme:' . $setting->name, $setting->default ?? false) == 1" :id="$setting->name" />
                        @else
                            <x-input :name="$setting->name" :type="$setting->type" :value="config('settings::theme:' . $setting->name, $setting->default ?? null)" :label="$setting->label"
                                :id="$setting->name" />
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
        <button type="submit" class="button button-primary float-right">{{ __('Save') }}</button>
        <button type="submit" name="reset" value="1" class="button button-danger float-right mr-4">{{ __('Reset') }}</button>
    </form>
</div>
