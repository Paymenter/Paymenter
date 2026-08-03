<div class="container py-10">
    <div class="grid lg:grid-cols-2 gap-8 items-stretch">
        <x-cyber.auth-aside />

        <form class="cyber-card cyber-clip cyber-neon flex flex-col gap-2 px-6 sm:px-12 py-12 w-full justify-center"
            wire:submit.prevent="submit" id="register">
            <div class="flex flex-col items-center mb-10">
                <x-logo class="h-14 mb-2" />
                <h1 class="text-3xl font-black text-center mt-6 cyber-neon-text">
                    <span class="cyber-glitch" data-text="{{ __('auth.sign_up_title') }}">{{ __('auth.sign_up_title') }}</span>
                </h1>
                <p class="text-sm text-base/55 mt-2">Crea tu cuenta en menos de un minuto</p>
            </div>

            <div class="flex flex-col md:grid md:grid-cols-2 gap-4">
                <x-form.input name="first_name" type="text" :label="__('general.input.first_name')"
                    :placeholder="__('general.input.first_name_placeholder')" wire:model="first_name" required />
                <x-form.input name="last_name" type="text" :label="__('general.input.last_name')"
                    :placeholder="__('general.input.last_name_placeholder')" wire:model="last_name" required />

                <x-form.input name="email" type="email" :label="__('general.input.email')"
                    :placeholder="__('general.input.email_placeholder')" required wire:model="email" divClass="col-span-2" />

                <x-form.input name="password" type="password" :label="__('general.input.password')" :placeholder="__('general.input.password_placeholder')"
                    wire:model="password" required />
                <x-form.input name="password_confirm" type="password" :label="__('general.input.password_confirmation')"
                    :placeholder="__('general.input.password_confirmation_placeholder')" wire:model="password_confirmation" required />

                <x-form.properties :custom_properties="$custom_properties" :properties="$properties" />

                @if(config('settings.tos'))
                    <x-form.checkbox wire:model="tos" name="tos" required>
                        {{ __('product.tos') }}
                        <a href="{{ config('settings.tos') }}" target="_blank" class="text-primary hover:text-primary/80">
                            {{ __('product.tos_link') }}
                        </a>
                    </x-form.checkbox>
                @endif
            </div>

            <x-captcha :form="'register'" />

            <x-button.primary class="w-full mt-4 cyber-sweep">{{ __('auth.sign_up') }}</x-button.primary>

            <div class="text-center rounded-md py-2 mt-6 text-sm text-base/70">
                {{ __('auth.already_have_account') }}
                <a class="text-primary font-semibold hover:underline" href="{{ route('login') }}" wire:navigate>
                    {{ __('auth.sign_in') }}
                </a>
            </div>
        </form>
    </div>
</div>
