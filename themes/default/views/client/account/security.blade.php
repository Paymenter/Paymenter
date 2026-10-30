<div class="container mt-14">
    <x-navigation.breadcrumb />
    <div class="px-2 flex flex-col gap-4">

        <!-- Sessions -->
        <div class="bg-background-secondary rounded-lg p-4">
            <h5 class="text-lg font-bold pb-3">{{ __('account.sessions') }}</h5>
            @foreach (Auth::user()->sessions as $session)
            <div class="flex flex-row items-center justify-between py-2 border-b border-base/50">
                <div>
                    <p class="text-sm text-primary-100">{{ $session->ip_address }} -
                        {{ $session->last_activity->diffForHumans() }}</p>
                    <p class="text-sm text-primary-400">{{ $session->formatted_device }}</p>
                </div>
                @if(!$session->is_current_device)
                <x-button.primary wire:click="logoutSession('{{ $session->id }}')" class="text-sm !w-fit">
                    {{ __('account.logout_sessions') }}
                </x-button.primary>
                @else
                <span class="text-sm text-primary-300 italic">{{ __('account.current_device') }}</span>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Change password -->
        <div class="bg-background-secondary rounded-lg p-4">
            <h5 class="text-lg font-bold pb-3">{{ __('account.change_password') }}</h5>
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
        </div>  

        @if($socialProviders->isNotEmpty())
        <div class="bg-background-secondary rounded-lg p-4">
            <h5 class="text-lg font-bold pb-1">{{ __('account.connected_accounts') }}</h5>
            <p class="text-sm text-primary-300 pb-3">{{ __('account.connected_accounts_description') }}</p>
            <div class="flex flex-col gap-3">
                @foreach($socialProviders as $provider => $socialProvider)
                <div class="flex flex-row items-center justify-between gap-4 py-2 border-b border-base/50 last:border-b-0">
                    <div>
                        <p class="text-sm text-primary-100">{{ $socialProvider['name'] }}</p>
                        @if($socialProvider['account'])
                        <p class="text-sm text-primary-400">{{ __('account.connected_since', ['date' => $socialProvider['account']->linked_at->translatedFormat('d. M Y')]) }}</p>
                        @else
                        <p class="text-sm text-primary-400">{{ __('account.not_connected') }}</p>
                        @endif
                    </div>
                    @if($socialProvider['account'])
                    <x-button.primary wire:click="startDisconnectSocialAccount('{{ $provider }}')" class="text-sm !w-fit">
                        {{ __('account.disconnect') }}
                    </x-button.primary>
                    @elseif($socialProvider['enabled'])
                    <a href="{{ route('oauth.connect', $provider) }}" class="text-sm text-secondary-500 text-secondary hover:underline">
                        {{ __('account.connect') }}
                    </a>
                    @else
                    <span class="text-sm text-primary-400">{{ __('account.not_available') }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        @if($showDisconnectSocialAccount)
        <x-modal :title="__('account.disconnect_account_title', ['provider' => ucfirst($disconnectingProvider)])" open="true">
            <p class="text-primary-100">{{ __('account.disconnect_account_description') }}</p>
            <form wire:submit.prevent="disconnectSocialAccount">
                <x-form.input divClass="mt-4" name="disconnectPassword" type="password"
                    :label="__('account.input.current_password')" :placeholder="__('account.input.current_password_placeholder')"
                    wire:model="disconnectPassword" autocomplete="current-password" required />
                <div class="flex gap-3 mt-4">
                    <x-button.primary type="submit" class="w-full">{{ __('account.disconnect') }}</x-button.primary>
                    <button type="button" class="w-full text-sm text-primary-100" wire:click="$set('showDisconnectSocialAccount', false)">{{ __('account.cancel') }}</button>
                </div>
            </form>
            <x-slot name="closeTrigger">
                <button wire:click="$set('showDisconnectSocialAccount', false)" class="text-primary-100">
                    <x-ri-close-fill class="size-6" />
                </button>
            </x-slot>
        </x-modal>
        @endif
        @endif

        <div class="bg-background-secondary rounded-lg p-4">
            <!-- Two factor authentication -->
            <h5 class="text-lg font-bold pb-3">{{ __('account.two_factor_authentication') }}</h5>
            @if ($twoFactorEnabled)
            <p class="text-sm text-primary-100">{{ __('account.two_factor_authentication_enabled') }}</p>
            <x-button.primary class="w-full mt-4" x-on:click="$store.confirmation.confirm({
                                title: '{{ __('account.two_factor_authentication_disable') }}',
                                message: '{{ __('account.two_factor_authentication_disable_description') }}',
                                confirmText: '{{ __('account.confirm') }}',
                                cancelText: '{{ __('account.cancel') }}',
                                callback: () => $wire.disableTwoFactor()
                            })">
                {{ __('account.two_factor_authentication_disable') }}
            </x-button.primary>
            @else
            <p class="text-sm text-primary-100">{{ __('account.two_factor_authentication_description') }}</p>
            <x-button.primary wire:click="enableTwoFactor" class="w-full mt-4">
                {{ __('account.two_factor_authentication_enable') }}
            </x-button.primary>
            @if ($showEnableTwoFactor)
            <x-modal :title="__('account.two_factor_authentication_enable')" open="true">
                <p class="text-primary-100">{{ __('account.two_factor_authentication_enable_description') }}</p>
                <div class="flex flex-col items-center mt-4">
                    <img src="{{ $twoFactorData['image'] }}" alt="QR code" class="w-64 h-64" />
                    <p class="text-primary-400 mt-2 text-sm text-center">
                        {{ __('account.two_factor_authentication_secret') }}<br />{{ $twoFactorData['secret'] }}</p>
                </div>
                <form wire:submit.prevent="enableTwoFactor">
                    <x-form.input divClass="mt-8" name="two_factor_code" type="text"
                        :label="__('account.input.two_factor_code')"
                        :placeholder="__('account.input.two_factor_code_placeholder')" wire:model="twoFactorCode" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code"
                        required />
                    <x-button.primary class="w-full mt-4" type="submit">
                        {{ __('account.two_factor_authentication_enable') }}
                    </x-button.primary>
                </form>
                <x-slot name="closeTrigger">
                    <button @click="document.location.reload()" class="text-primary-100">
                        <x-ri-close-fill class="size-6" />
                    </button>
                </x-slot>
            </x-modal>
            @endif
            @endif
        </div>
    </div>
</div>
