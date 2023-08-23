<div class="hidden mt-3" id="tab-affiliate">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.affiliate') }}">
        @csrf
        <div class="grid grid-cols-1">
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('affiliate') is-invalid @enderror"
                    placeholder=" " name="affiliate" value="1" {{ config('settings::affiliate') ? 'checked' : '' }}
                    data-popover-target="affiliateh" />
                <label class="form-label" style="position: unset;">{{ __('Affiliate System enabled') }}</label>
                <div id="affiliateh" role="tooltip" data-popover
                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    {{ __('Affiliates can earn money by referring users to the site.') }}
                    <div data-popper-arrow></div>
                </div>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('affiliate_percentage') is-invalid @enderror"
                    placeholder=" " name="affiliate_percentage"
                    value="{{ config('settings::affiliate_percentage') }}" />
                <label class="form-label">{{ __('Earning Percentage %') }}</label>
            </div>
            <div class="relative m-4 group">
                <select class="form-input peer @error('affiliate_type') is-invalid @enderror"
                    placeholder=" " name="affiliate_type" value="{{ config('settings::affiliate_type') }}">
                    <option value="random" {{ config('settings::affiliate_type') == 'random' ? 'selected' : '' }}>
                        {{ __('Random Link') . ' ' . url('asde2e') }}</option>
                    <option value="fixed" {{ config('settings::affiliate_type') == 'fixed' ? 'selected' : '' }}>
                        {{ __('Fixed Link') . ' ' . url(str_replace(' ', '', auth()->user()->name)) }}</option>
                    <option value="custom" {{ config('settings::affiliate_type') == 'custom' ? 'selected' : '' }}>
                        {{ __('Custom Link (The user sets his own code)') }}</option>
                </select>

                <label class="form-label">{{ __('Affiliate Type') }}</label>
            </div>
        </div>
        <button class="float-right button button-primary">{{ __('Submit') }}</button>
    </form>

</div>
