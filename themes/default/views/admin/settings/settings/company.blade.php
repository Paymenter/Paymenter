<div class="hidden mt-3" id="tab-company">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.company') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2">
            <h2 class="col-span-1 md:col-span-2 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('Company Details (Optional)') }}</h2>
            <div class="md:grid grid-cols-2 m-4 gap-x-2">
                <div class="relative group">
                    <input type="text" class="form-input peer @error('company_name') is-invalid @enderror" placeholder=" "
                           name="company_name" value="{{ config('settings::company_name') }}" />
                    <label class="form-label">{{ __('Name') }}</label>
                </div>
                <div class="relative group">
                    <input type="text" class="form-input peer @error('company_tin') is-invalid @enderror" placeholder=" "
                           name="company_tin" value="{{ config('settings::company_tin') }}" />
                    <label class="form-label">{{ __('TIN') }}</label>
                </div>
            </div>
            <div class="md:grid grid-cols-2 m-4 gap-x-2">
                <div class="relative group">
                    <input type="text" class="form-input peer @error('company_country') is-invalid @enderror" placeholder=" "
                           name="company_country" value="{{ config('settings::company_country') }}" />
                    <label class="form-label">{{ __('Country') }}</label>
                </div>
                <div class="relative group">
                    <input type="text" class="form-input peer @error('company_address') is-invalid @enderror" placeholder=" "
                           name="company_address" value="{{ config('settings::company_address') }}" />
                    <label class="form-label">{{ __('Address') }}</label>
                </div>
            </div>
            <div class="md:grid grid-cols-2 m-4 gap-x-2">
                <div class="relative group">
                    <input type="text" class="form-input peer @error('company_phone') is-invalid @enderror" placeholder=" "
                           name="company_phone" value="{{ config('settings::company_phone') }}" />
                    <label class="form-label">{{ __('Phone') }}</label>
                </div>
                <div class="relative group">
                    <input type="text" class="form-input peer @error('company_email') is-invalid @enderror"
                           placeholder=" " name="company_email" value="{{ config('settings::company_email') }}" />
                    <label class="form-label">{{ __('Email') }}</label>
                </div>
            </div>
            <div class="md:grid grid-cols-2 m-4 gap-x-2">
                <div class="relative group">
                    <input type="text" class="form-input peer @error('company_city') is-invalid @enderror" placeholder=" "
                           name="company_city" value="{{ config('settings::company_city') }}" />
                    <label class="form-label">{{ __('City') }}</label>
                </div>
                <div class="relative group">
                    <input type="text" class="form-input peer @error('company_zip') is-invalid @enderror" placeholder=" "
                           name="company_zip" value="{{ config('settings::company_zip') }}" />
                    <label class="form-label">{{ __('Zip') }}</label>
                </div>
            </div>
        </div>
        <button class="float-right form-submit">{{ __('Submit') }}</button>
    </form>
</div>
