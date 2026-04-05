<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-20 md:mt-24 mb-16 animate-in fade-in duration-700">
    <x-navigation.breadcrumb />

    <div class="mt-6 md:mt-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-6 animate-in slide-in-from-top-4 duration-1000">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-px bg-gradient-to-r from-primary-500 to-transparent"></div>
                    <p class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-[0.3em]">{{ __('account.security') }}</p>
                </div>
                <h1 class="text-2xl md:text-3xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                    {{ __('account.security_settings') }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Manage your active sessions and protect your account.</p>
                <div class="w-12 h-0.5 bg-primary-500 rounded-full mt-4"></div>
            </div>
        </div>

        <div class="flex flex-col gap-8">
            {{-- Active Sessions Section --}}
            <div class="bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 rounded-[2rem] p-6 md:p-8 shadow-md animate-in slide-in-from-bottom-4 duration-700">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-950/50 rounded-xl flex items-center justify-center">
                        <x-ri-shield-user-line class="size-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <h3 class="text-lg font-black tracking-tighter text-gray-900 dark:text-white uppercase">{{ __('account.sessions') }}</h3>
                </div>

                <div class="grid gap-4">
                    @foreach (Auth::user()->sessions->filter(fn ($session) => !$session->impersonating()) as $session)
                    <div class="group flex flex-row items-center justify-between p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/50 hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-300">
                        <div class="flex items-center gap-4">
                            <div class="size-11 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 flex items-center justify-center text-primary-500 group-hover:scale-110 transition-transform">
                                @if($session->formatted_device === 'Mobile')
                                    <x-ri-smartphone-line class="size-5" />
                                @else
                                    <x-ri-computer-line class="size-5" />
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-900 dark:text-white tracking-tight">{{ $session->ip_address }}</p>
                                <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mt-0.5">
                                    {{ $session->formatted_device }} • {{ $session->last_activity->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <x-button.primary wire:click="logoutSession('{{ $session->id }}')" 
                            class="!py-2 !px-4 text-[10px] font-black uppercase tracking-widest !w-fit opacity-0 group-hover:opacity-100 shadow-lg shadow-primary-500/10 transition-all">
                            {{ __('account.logout_sessions') }}
                        </x-button.primary>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                {{-- Change Password Section --}}
                <div class="bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 rounded-[2rem] p-8 shadow-md h-full">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-950/50 rounded-xl flex items-center justify-center">
                            <x-ri-lock-password-line class="size-5 text-primary-600 dark:text-primary-400" />
                        </div>
                        <h3 class="text-lg font-black tracking-tighter text-gray-900 dark:text-white uppercase">{{ __('account.change_password') }}</h3>
                    </div>

                    <form wire:submit="changePassword" class="space-y-4">
                        <x-form.input name="current_password" type="password"
                            class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl"
                            :label="__('account.input.current_password')"
                            wire:model="current_password" required />
                        
                        <div class="grid grid-cols-1 gap-4">
                            <x-form.input name="password" type="password" 
                                class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl"
                                :label="__('account.input.new_password')"
                                wire:model="password" required />
                            
                            <x-form.input name="password_confirmation" type="password" 
                                class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl"
                                :label="__('account.input.confirm_password')"
                                wire:model="password_confirmation" required />
                        </div>

                        <x-button.primary type="submit" class="w-full !py-4 text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-primary-500/20 mt-2 hover:scale-[1.02] active:scale-95 transition-all">
                            {{ __('account.change_password') }}
                        </x-button.primary>
                    </form>
                </div>

                {{-- 2FA Section --}}
                <div class="bg-white dark:bg-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-800 rounded-[2rem] p-8 shadow-md h-full">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-950/50 rounded-xl flex items-center justify-center">
                            <x-ri-shield-flash-line class="size-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h3 class="text-lg font-black tracking-tighter text-gray-900 dark:text-white uppercase">{{ __('account.two_factor_authentication') }}</h3>
                    </div>

                    @if ($twoFactorEnabled)
                    <div class="p-6 rounded-[2rem] bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-800 text-center">
                        <div class="size-14 rounded-full bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center mx-auto mb-4 border border-emerald-200 dark:border-emerald-800">
                            <x-ri-shield-check-line class="size-7 text-emerald-500" />
                        </div>
                        <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.2em] mb-6">
                            {{ __('account.two_factor_authentication_enabled') }}
                        </p>
                        <x-button.primary class="w-full !bg-red-500 hover:!bg-red-600 !border-none !py-4 text-[10px] font-black uppercase tracking-widest shadow-xl shadow-red-500/20" 
                            x-on:click="$store.confirmation.confirm({
                                title: '{{ __('account.two_factor_authentication_disable') }}',
                                message: '{{ __('account.two_factor_authentication_disable_description') }}',
                                callback: () => $wire.disableTwoFactor()
                            })">
                            {{ __('account.two_factor_authentication_disable') }}
                        </x-button.primary>
                    </div>
                    @else
                    <div class="flex flex-col h-full justify-between gap-6">
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest leading-relaxed">
                            {{ __('account.two_factor_authentication_description') }}
                        </p>
                        <x-button.primary wire:click="enableTwoFactor" class="w-full !py-4 text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-primary-500/20 hover:scale-[1.02] active:scale-95 transition-all">
                            <x-ri-shield-keyhole-line class="size-4 mr-2" />
                            {{ __('account.two_factor_authentication_enable') }}
                        </x-button.primary>
                    </div>

                    {{-- 2FA Enable Modal --}}
                    @if ($showEnableTwoFactor)
                    <x-modal :title="__('account.two_factor_authentication_enable')" open="true">
                        <x-slot name="closeTrigger">
                            <button @click="document.location.reload()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                                <x-ri-close-fill class="size-6 text-gray-400" />
                            </button>
                        </x-slot>
                        
                        <div class="space-y-6 text-center py-4">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">
                                {{ __('account.two_factor_authentication_enable_description') }}
                            </p>
                            
                            <div class="p-4 bg-white rounded-[2.5rem] border border-gray-200 inline-block mx-auto shadow-inner">
                                <img src="{{ $twoFactorData['image'] }}" alt="QR code" class="size-48" />
                            </div>
                            
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 select-all">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-1">{{ __('account.two_factor_authentication_secret') }}</p>
                                <code class="text-sm font-black text-primary-500 tracking-widest">{{ $twoFactorData['secret'] }}</code>
                            </div>

                            <form wire:submit.prevent="enableTwoFactor" class="text-left mt-8">
                                <x-form.input name="two_factor_code" type="text"
                                    class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 !rounded-xl text-center text-xl font-black tracking-[0.5em] focus:!border-primary-500"
                                    :label="__('account.input.two_factor_code')"
                                    :placeholder="'000000'" wire:model="twoFactorCode" required />
                                
                                <x-button.primary class="w-full mt-6 !py-4 text-xs font-black uppercase tracking-widest shadow-xl shadow-primary-500/20" type="submit">
                                    {{ __('account.two_factor_authentication_enable') }}
                                </x-button.primary>
                            </form>
                        </div>
                    </x-modal>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>