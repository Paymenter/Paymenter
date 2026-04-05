<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-20 md:mt-24 mb-16 animate-in fade-in duration-700">
    <x-navigation.breadcrumb />
    
    <div class="mt-6 md:mt-8 p-6 md:p-8 lg:p-10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl">
        
        {{-- Header Section --}}
        <div class="mb-8 md:mb-10 animate-in slide-in-from-top-4 duration-1000">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-px bg-gradient-to-r from-primary-500 to-transparent"></div>
                <p class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-[0.3em]">Profile</p>
            </div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                {{ __('auth.profile_settings') }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                {{ __('auth.manage_your_personal_information') }}
            </p>
            <div class="w-16 h-0.5 bg-gradient-to-r from-primary-500 to-transparent rounded-full mt-4"></div>
        </div>

        {{-- Profile Form --}}
        <form wire:submit.prevent="submit" class="space-y-6">
            <div class="grid md:grid-cols-2 gap-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
                
                {{-- First Name --}}
                <div class="group">
                    <x-form.input 
                        name="first_name" 
                        type="text" 
                        class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl !py-3 transition-all"
                        :label="__('general.input.first_name')"
                        :placeholder="__('general.input.first_name_placeholder')" 
                        wire:model="first_name" 
                        required 
                        dirty 
                    />
                </div>

                {{-- Last Name --}}
                <div class="group">
                    <x-form.input 
                        name="last_name" 
                        type="text" 
                        class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl !py-3 transition-all"
                        :label="__('general.input.last_name')"
                        :placeholder="__('general.input.last_name_placeholder')" 
                        wire:model="last_name" 
                        required 
                        dirty 
                    />
                </div>

                {{-- Email --}}
                <div class="md:col-span-2 group">
                    <x-form.input 
                        name="email" 
                        type="email" 
                        class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl !py-3 transition-all"
                        :label="__('general.input.email')"
                        :placeholder="__('general.input.email_placeholder')" 
                        required 
                        wire:model="email" 
                        dirty 
                    />
                </div>

                {{-- Custom Properties --}}
                @if(count($custom_properties) > 0)
                <div class="md:col-span-2 pt-2">
                    <div class="bg-gray-50 dark:bg-gray-800/30 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                            <x-ri-profile-line class="size-4 text-primary-500" />
                            <h3 class="text-xs font-black uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Additional Information
                            </h3>
                        </div>
                        <x-form.properties :custom_properties="$custom_properties" :properties="$properties" />
                    </div>
                </div>
                @endif
            </div>

            {{-- Form Actions --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8 pt-4 border-t border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <x-ri-shield-check-line class="size-4 text-green-500" />
                    <span>Your information is secure and encrypted</span>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" wire:navigate class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition-all duration-200">
                        Cancel
                    </a>
                    <x-button.primary type="submit" 
                        class="px-8 py-3 text-xs font-black uppercase tracking-[0.3em] shadow-lg shadow-primary-500/20 hover:scale-105 active:scale-95 transition-all duration-200">
                        <x-loading target="submit" />
                        <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                            <x-ri-save-line class="size-4" />
                            {{ __('general.update') }}
                        </span>
                    </x-button.primary>
                </div>
            </div>
        </form>
        
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
    
    {{-- Danger Zone (Optional) --}}
    <div class="mt-8 p-6 md:p-8 bg-red-50/50 dark:bg-red-950/20 rounded-3xl border border-red-200 dark:border-red-800">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <x-ri-error-warning-line class="size-5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-red-700 dark:text-red-400">Danger Zone</h3>
                    <p class="text-xs text-red-600 dark:text-red-300 mt-0.5">Once you delete your account, there is no going back.</p>
                </div>
            </div>
            <button 
                x-on:click.prevent="$store.confirmation.confirm({
                    title: 'Delete Account',
                    text: 'Are you sure you want to delete your account? This action cannot be undone.',
                    confirmButtonText: 'Yes, delete my account',
                    cancelButtonText: 'Cancel',
                    callback: () => $wire.deleteAccount()
                })"
                class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all duration-200 hover:scale-105 active:scale-95">
                Delete Account
            </button>
        </div>
    </div>
</div>