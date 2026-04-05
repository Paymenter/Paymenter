<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-20 md:mt-24 mb-16 animate-in fade-in duration-700">
    <x-navigation.breadcrumb />

    <div class="mt-6 md:mt-8">
        
        {{-- Push Notifications Section --}}
        @if($this->supportsPush())
        <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-2xl p-6 md:p-8 border border-gray-200 dark:border-gray-800 shadow-xl mb-8 animate-in slide-in-from-top-4 duration-1000" x-data="pushNotifications">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-950/50 rounded-xl flex items-center justify-center">
                        <x-ri-notification-4-line class="size-6 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h2 class="text-lg md:text-xl font-black tracking-tighter text-gray-900 dark:text-white mb-1">
                            {{ __('account.push_notifications') }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md">
                            {{ __('account.push_notifications_description') }}
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-col items-end gap-2">
                    <x-button.primary type="button" class="w-full md:w-auto !py-3 !px-6 text-[10px] font-black uppercase tracking-[0.2em] shadow-lg transition-all hover:scale-105 active:scale-95" 
                        @click="subscribe"
                        x-bind:disabled="subscriptionStatus !== 'not_subscribed'">
                        <x-ri-notification-line class="size-4 mr-2" />
                        <span x-show="subscriptionStatus === 'not_subscribed'">{{ __('account.enable_push_notifications') }}</span>
                        <span x-show="subscriptionStatus === 'subscribed'">Notifications Enabled</span>
                    </x-button.primary>

                    <div x-show="subscriptionStatus !== 'unknown'" class="animate-in fade-in duration-500">
                        <template x-if="subscriptionStatus === 'not_supported'">
                            <p class="text-[10px] font-black text-red-500 dark:text-red-400 uppercase tracking-wider flex items-center gap-1">
                                <x-ri-error-warning-line class="size-3" />
                                {{ __('account.push_status.not_supported') }}
                            </p>
                        </template>
                        <template x-if="subscriptionStatus === 'denied'">
                            <p class="text-[10px] font-black text-red-500 dark:text-red-400 uppercase tracking-wider flex items-center gap-1">
                                <x-ri-error-warning-line class="size-3" />
                                {{ __('account.push_status.denied') }}
                            </p>
                        </template>
                        <template x-if="subscriptionStatus === 'subscribed'">
                            <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                                <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ __('account.push_status.subscribed') }}
                            </p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        @script
        <script>
            Alpine.data('pushNotifications', () => ({
                subscriptionStatus: 'unknown',

                init() {
                    if ('serviceWorker' in navigator && 'PushManager' in window) {
                        navigator.serviceWorker.ready.then((registration) => {
                            registration.pushManager.getSubscription().then((subscription) => {
                                if (subscription) {
                                    this.subscriptionStatus = 'subscribed';
                                } else {
                                    this.subscriptionStatus = Notification.permission === 'denied' ? 'denied' : 'not_subscribed';
                                }
                            });
                        });
                    } else {
                        this.subscriptionStatus = 'not_supported';
                    }
                },

                subscribe() {
                    if ('serviceWorker' in navigator && 'PushManager' in window) {
                        navigator.serviceWorker.ready.then((registration) => {
                            registration.pushManager.getSubscription().then((subscription) => {
                                if (subscription) {
                                    @this.call('storePushSubscription', JSON.stringify(subscription));
                                    this.subscriptionStatus = 'subscribed';
                                    return;
                                }

                                registration.pushManager.subscribe({
                                    userVisibleOnly: true,
                                    applicationServerKey: urlBase64ToUint8Array('{{ config('settings.vapid_public_key') }}')
                                }).then((newSubscription) => {
                                    @this.call('storePushSubscription', JSON.stringify(newSubscription));
                                    this.subscriptionStatus = 'subscribed';
                                }).catch((e) => {
                                    if (Notification.permission === 'denied') {
                                        this.subscriptionStatus = 'denied';
                                    } else {
                                        this.subscriptionStatus = 'not_subscribed';
                                    }
                                });
                            });
                        });
                    } else {
                        this.subscriptionStatus = 'not_supported';
                    }
                }
            }));

            function urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }
        </script>
        @endscript
        @endif

        {{-- Notification Preferences Table --}}
        <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                            <th class="py-5 px-6">
                                <div class="flex items-center gap-2">
                                    <x-ri-notification-3-line class="size-4 text-primary-500" />
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-600 dark:text-gray-400">{{ __('account.notification') }}</span>
                                </div>
                                <p class="text-[9px] font-medium text-gray-500 dark:text-gray-500 mt-1">
                                    {{ __('account.notifications_description') }}
                                </p>
                            </th>
                            <th class="py-5 px-4 text-center w-24">
                                <div class="flex flex-col items-center gap-1">
                                    <x-ri-mail-line class="size-4 text-primary-500" />
                                    <span class="text-[9px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('account.email') }}</span>
                                </div>
                            </th>
                            <th class="py-5 px-4 text-center w-24 border-l border-gray-200 dark:border-gray-800">
                                <div class="flex flex-col items-center gap-1">
                                    <x-ri-notification-line class="size-4 text-emerald-500" />
                                    <span class="text-[9px] font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('account.in_app') }}</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody x-data="{ preferences: $wire.entangle('preferences') }" class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($this->notifications as $notification)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-all duration-200">
                            <td class="py-4 px-6">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                                    {{ $notification->name }}
                                </span>
                                @if(isset($notification->description) && $notification->description)
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $notification->description }}
                                    </p>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex justify-center">
                                    <x-form.toggle 
                                        :disabled="!$notification->mail_controllable"
                                        wire:model.defer="preferences.{{ $notification->key }}.mail_enabled"
                                    />
                                </div>
                            </td>
                            <td class="py-4 px-4 border-l border-gray-100 dark:border-gray-800">
                                <div class="flex justify-center">
                                    <x-form.toggle 
                                        :disabled="!$notification->in_app_controllable"
                                        wire:model.defer="preferences.{{ $notification->key }}.in_app_enabled"
                                    />
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <x-ri-notification-off-line class="size-12 text-gray-400" />
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No notification templates found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="mt-8 flex justify-end animate-in fade-in slide-in-from-bottom-4 duration-700">
            <x-button.primary wire:click="savePreferences" 
                class="w-full md:w-auto !px-8 md:!px-12 !py-3.5 text-xs font-black uppercase tracking-[0.3em] shadow-lg shadow-primary-500/20 hover:scale-105 active:scale-95 transition-all duration-200" 
                wire:loading.attr="disabled">
                <x-loading wire:loading wire:target="savePreferences" />
                <span wire:loading.remove wire:target="savePreferences" class="flex items-center gap-2">
                    <x-ri-save-line class="size-4" />
                    {{ __('general.save_changes') }}
                </span>
            </x-button.primary>
        </div>
        
        {{-- Success Message --}}
        @if(session()->has('message'))
            <div class="mt-6 p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl animate-in slide-in-from-top-2 duration-300">
                <div class="flex items-center gap-3">
                    <x-ri-checkbox-circle-fill class="size-5 text-emerald-500" />
                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ session('message') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>