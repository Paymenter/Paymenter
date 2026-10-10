<div class="container mt-14">
    <x-navigation.breadcrumb />
    <div class="px-2 flex flex-col gap-4">

        <!-- Passkeys -->
        <div class="bg-background-secondary rounded-lg p-4" x-data="passkeyManager">
            <h5 class="text-lg font-bold pb-2">{{ __('account.passkeys') }}</h5>
            <p class="text-sm text-primary-300 mb-3">{{ __('account.passkeys_description') }}</p>

            <div x-show="supported" class="flex flex-col sm:flex-row gap-3">
                <x-form.input name="passkey_name" type="text" :label="__('account.passkey_name')"
                    :placeholder="__('account.passkey_name_placeholder')" x-model="name" maxlength="255" />
                <x-button.primary type="button" class="sm:mt-6" x-on:click="register"
                    x-bind:disabled="loading || !name.trim()">
                    {{ __('account.add_passkey') }}
                </x-button.primary>
            </div>
            <p x-cloak x-show="!supported" class="text-sm text-primary-400">
                {{ __('account.passkeys_unsupported') }}
            </p>

            <p x-show="error" x-text="error" role="alert" class="mt-2 text-sm text-red-400"></p>
            <p class="text-xs text-primary-400 mt-2">
                {{ __('account.passkey_confirmation_required') }}
                <a href="{{ route('password.confirm') }}" wire:navigate class="text-secondary hover:underline">
                    {{ __('account.confirm_password') }}
                </a>
            </p>

            <div class="mt-4">
                @forelse ($passkeys as $passkey)
                <div class="flex flex-row items-center justify-between gap-3 py-3 border-b border-base/50">
                    <div>
                        <p class="text-sm font-medium text-primary-100">{{ $passkey->name }}</p>
                        <p class="text-xs text-primary-400">
                            {{ __('account.passkey_added', ['date' => $passkey->created_at->diffForHumans()]) }}
                            @if ($passkey->last_used_at)
                            · {{ __('account.passkey_last_used', ['date' => $passkey->last_used_at->diffForHumans()]) }}
                            @endif
                        </p>
                    </div>
                    <x-button.primary type="button" class="text-sm !w-fit" x-on:click="$store.confirmation.confirm({
                        title: @js(__('account.remove_passkey')),
                        message: @js(__('account.remove_passkey_confirm', ['name' => $passkey->name])),
                        confirmText: @js(__('account.confirm')),
                        cancelText: @js(__('account.cancel')),
                        callback: () => remove(@js($passkey->id))
                    })" x-bind:disabled="loading">
                        {{ __('account.remove_passkey') }}
                    </x-button.primary>
                </div>
                @empty
                <p class="text-sm text-primary-400 py-2">{{ __('account.no_passkeys') }}</p>
                @endforelse
            </div>
        </div>

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
