<div>
    <div class="flex min-h-[100vh]">
        <div class="justify-center flex flex-1 min-h-full flex-col mx-4">
            <div class="mx-auto container">
                <div
                    class="mx-auto flex flex-col gap-2 mt-4 shadow-sm px-6 sm:px-14 pb-10 bg-white dark:bg-primary-800 rounded-md xl:max-w-[40%] w-full">

                    <form class="flex flex-col gap-2" method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="flex flex-col items-center mt-4 mb-10">
                            <x-logo />
                            <h1 class="text-2xl text-center dark:text-white mt-2">{{ __('Create an account') }} </h1>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <x-form.input name="first_name" type="text" :label="__('First name')" :placeholder="__('Your first name')"
                                :required="!in_array('first_name', config('settings.optional_fields'))" />
                            <x-form.input name="last_name" type="text" :label="__('Last name')" :placeholder="__('Your last name')"
                                :required="!in_array('last_name', config('settings.optional_fields'))" />

                            <x-form.input name="email" type="email" :label="__('Email')" :placeholder="__('Your email')" />
                            <x-form.input name="phone" type="phone" :label="__('Phone number')" :placeholder="__('Your phone number')"
                                :required="!in_array('phone', config('settings.optional_fields'))" />

                            <x-form.input name="company_name" type="text" :label="__('Company name')" :placeholder="__('Your company name')"
                                :required="!in_array('company_name', config('settings.optional_fields'))" />
                            <x-form.input name="country" type="text" :label="__('Country')" :placeholder="__('Your country')"
                                :required="!in_array('country', config('settings.optional_fields'))" />
                            <x-form.input name="address" type="text" :label="__('Address')" :placeholder="__('Your address')"
                                :required="!in_array('address', config('settings.optional_fields'))" divClass="col-span-2" />
                            <x-form.input name="address2" type="text" :label="__('Address 2')" :placeholder="__('Your address 2')"
                                :required="!in_array('address2', config('settings.optional_fields'))" divClass="col-span-2" />

                            <div class="grid grid-cols-3 col-span-2 gap-4">
                                <x-form.input name="city" type="text" :label="__('City')" :placeholder="__('Your city')"
                                    :required="!in_array('city', config('settings.optional_fields'))" />
                                <x-form.input name="state" type="text" :label="__('State')" :placeholder="__('Your address')"
                                    :required="!in_array('state', config('settings.optional_fields'))" />
                                <x-form.input name="zip" type="text" :label="__('Zip')" :placeholder="__('Your zip')"
                                    :required="!in_array('zip', config('settings.optional_fields'))" />
                            </div>

                            <x-form.input name="password" type="password" :label="__('Password')" :placeholder="__('Your password')"
                                required />
                            <x-form.input name="password_confirm" type="password" :label="__('Password')" :placeholder="__('Confirm your password')"
                                required />

                        </div>

                        <x-captcha :form="'login'" />

                        <x-button.primary class="w-full">{{ __('Sign up') }}</x-button.primary>

                        <div class="dark:text-white text-center rounded-md py-2 mt-6 text-sm">
                            {{ __('Already have an account?') }}
                            <a class="text-sm text-secondary-500 text-secondary hover:underline"
                                href="{{ route('login') }}" wire:navigate>
                                {{ __('Sign in') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
