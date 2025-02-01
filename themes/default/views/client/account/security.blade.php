<div>
    <x-navigation.breadcrumb />
    <div class="px-2">

        <!-- Sessions -->
        <h5 class="text-lg font-bold pb-3 pt-4">{{ __('account.sessions') }}</h5>
        @foreach (Auth::user()->sessions as $session)
        <div class="flex flex-row items-center justify-between py-2 border-b border-primary-700">
            <div>
                <p class="text-sm text-primary-100">{{ $session->ip_address }} -
                    {{ $session->last_activity->diffForHumans() }}</p>
                <p class="text-sm text-primary-400">{{ $session->formatted_device }}</p>
            </div>
            <x-button.primary wire:click="logoutSession('{{ $session->id }}')" class="text-sm !w-fit">
                {{ __('account.logout_sessions') }}
            </x-button.primary>
        </div>
        @endforeach

        <!-- Change password -->
        <h5 class="text-lg font-bold pb-3 pt-10">{{ __('account.change_password') }}</h5>
        <form wire:submit="changePassword">
            <div class="grid grid-cols-2 gap-4">
                <x-form.input divClass="col-span-2" name="current_password" type="password"
                    :label="__('account.input.current_password')"
                    :placeholder="__('account.input.current_password_placeholder')" wire:model="current_password"
                    required />
                <x-form.input name="password" type="password" :label="__('account.input.new_password')"
                    :placeholder="__('account.input.new_password_placeholder')" wire:model="password" required />
                <x-form.input name="password_confirmation" type="password" :label="__('account.input.confirm_password')"
                    :placeholder="__('account.input.confirm_password_placeholder')" wire:model="password_confirmation"
                    required />
            </div>

            <x-button.primary type="submit" class="w-full mt-4">
                {{ __('account.change_password') }}
            </x-button.primary>
        </form>

        <!-- Two factor authentication -->
        <h5 class="text-lg font-bold pb-3 pt-10">{{ __('account.two_factor_authentication') }}</h5>
        @if ($twoFactorEnabled)
        <p class="text-sm text-primary-100">{{ __('account.two_factor_authentication_enabled') }}</p>
        <x-button.primary wire:click="disableTwoFactor" class="w-full mt-4">
            {{ __('Disable two factor authentication') }}
        </x-button.primary>
        @else
        <p class="text-sm text-primary-100">{{ __('account.two_factor_authentication_description') }}</p>
        <x-button.primary wire:click="enableTwoFactor" class="w-full mt-4">
            {{ __('Enable two factor authentication') }}
        </x-button.primary>
        @if ($showEnableTwoFactor)
        <x-modal title="Enable two factor authentication" open="true">
            <p class="text-primary-100">{{ __('account.two_factor_authentication_enable_description') }}</p>
            <div class="flex flex-col items-center mt-4">
                <img src="{{ $twoFactorData['image'] }}" alt="QR code" class="w-64 h-64" />
                <p class="text-primary-400 mt-2 text-sm text-center">
                    {{ __('account.two_factor_authentication_secret') }}<br />{{ $twoFactorData['secret'] }}</p>
            </div>
            <form wire:submit.prevent="enableTwoFactor">
                <x-form.input divClass="mt-8" name="two_factor_code" type="text"
                    :label="__('account.input.two_factor_code')"
                    :placeholder="__('account.input.two_factor_code_placeholder')" wire:model="twoFactorCode"
                    required />
                <x-button.primary class="w-full mt-4" type="submit">
                    {{ __('Enable two factor authentication') }}
                </x-button.primary>
            </form>
            <x-slot name="closeTrigger">
                <button @click="document.location.reload()" class="text-primary-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </x-slot>
        </x-modal>
        @endif
        @endif
    </div>
</div>