<div class="hidden mt-3" id="tab-company">
    <form method="POST" enctype="multipart/form-data" class="mb-3" action="{{ route('admin.settings.company') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <h2 class="col-span-1 md:col-span-2 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('Company Details (Optional)') }}</h2>
            <div class="md:grid grid-cols-2 gap-x-2">
                <x-input type="text" label="{{ __('Name') }}" placeholder="{{ __('Name..') }}"
                         name="company_name" value="{{ config('settings::company_name') }}" />

                <x-input type="text" label="{{ __('VAT') }}" placeholder="{{ __('VAT..') }}"
                    name="company_vat" value="{{ config('settings::company_vat') }}" />
            </div>
            <div class="md:grid grid-cols-2 gap-x-2">
                <x-input type="select" placeholder="{{ __('Country') }}" name="company_country" id="company_country" label="{{ __('Country') }}">
                    @foreach (App\Classes\Constants::countries() as $country)
                        <option value="{{ $country }}" @if(config('settings::company_country') == $country) selected @endif>
                            {{ $country }}
                        </option>
                    @endforeach
                </x-input>
                <x-input type="text" label="{{ __('Address') }}" placeholder="{{ __('Address..') }}"
                         name="company_address" id="company_address" icon="ri-home-4-line" value="{{ config('settings::company_address') }}" />
            </div>
            <div class="md:grid grid-cols-2 gap-x-2">
                <x-input type="text" label="{{ __('Phone') }}" placeholder="{{ __('Phone..') }}"
                         name="company_phone" id="company_phone" icon="ri-phone-line" value="{{ config('settings::company_phone') }}" />
                <x-input type="text" label="{{ __('Email') }}" placeholder="{{ __('Email..') }}"
                            name="company_email" id="company_email" icon="ri-mail-line" value="{{ config('settings::company_email') }}" />
            </div>
            <div class="md:grid grid-cols-2 gap-x-2">
                <x-input type="text" label="{{ __('City') }}" placeholder="{{ __('City..') }}"
                         name="company_city" id="company_city" icon="ri-building-2-line" value="{{ config('settings::company_city') }}" />
                <x-input type="text" label="{{ __('Zip') }}" placeholder="{{ __('Zip..') }}"
                            name="company_zip" id="company_zip" icon="ri-building-2-line" value="{{ config('settings::company_zip') }}" />
            </div>
        </div>
        <button class="float-right form-submit">{{ __('Submit') }}</button>
    </form>
</div>
