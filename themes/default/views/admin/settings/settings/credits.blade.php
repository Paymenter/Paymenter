<div class="hidden mt-3" id="tab-credits">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.credits') }}">
        @csrf
        <div class="grid grid-cols-1">
            <div class="relative m-4 group">
                <input type="checkbox" class="form-input w-fit peer @error('credits') is-invalid @enderror"
                    placeholder=" " name="credits" value="1" {{ config('settings::credits') ? 'checked' : '' }}
                    data-popover-target="creditsh" />
                <label class="form-label" style="position: unset;">{{ __('Credit system enabled') }}</label>
                <div id="creditsh" role="tooltip" data-popover
                    class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                    {{ __('Check to enable adding of funds by clients from the client area.') }}
                    <div data-popper-arrow></div>
                </div>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('minimum_deposit') is-invalid @enderror"
                    placeholder=" " name="minimum_deposit" value="{{ config('settings::minimum_deposit') }}" />
                <label class="form-label" style="position: unset;">{{ __('Minimum deposit') }}</label>
            </div>
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('maximum_deposit') is-invalid @enderror"
                    placeholder=" " name="maximum_deposit" value="{{ config('settings::maximum_deposit') }}" />
                <label class="form-label" style="position: unset;">{{ __('Maximum deposit') }}</label>
            </div>
            <!-- Maximum credit balance -->
            <div class="relative m-4 group">
                <input type="text" class="form-input peer @error('maximum_balance') is-invalid @enderror"
                    placeholder=" " name="maximum_balance" value="{{ config('settings::maximum_balance') }}" />
                <label class="form-label" style="position: unset;">{{ __('Maximum credit balance') }}</label>
            </div>
        </div>
        <button class="float-right button button-primary">{{ __('Submit') }}</button>
    </form>
</div>
    