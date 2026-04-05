<form 
    class="mx-auto flex flex-col gap-6 mt-12 px-8 sm:px-16 pb-12 bg-white dark:bg-gray-900/50 backdrop-blur-sm rounded-[2.5rem] border border-gray-200 dark:border-gray-800 shadow-2xl shadow-primary-500/5 xl:max-w-[55%] w-full animate-in fade-in zoom-in-95 duration-700" 
    wire:submit.prevent="submit" 
    id="register">

    {{-- Header Section --}}
    <div class="flex flex-col items-center mt-12 mb-8 animate-in slide-in-from-top-4 duration-1000 delay-100">
        <div class="relative group mb-6">
            <div class="absolute -inset-1 bg-gradient-to-r from-primary-500 to-brand-teal rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
            <x-logo class="relative h-12" />
        </div>
        
        <h1 class="text-3xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent uppercase text-center">
            {{ __('auth.sign_up_title') }}
        </h1>
        <div class="w-12 h-1 bg-primary-500 rounded-full mt-3"></div>
    </div>

    {{-- Inputs Grid --}}
    <div class="flex flex-col md:grid md:grid-cols-2 gap-x-6 gap-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-300">
        
        <x-form.input name="first_name" type="text" 
            class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl !py-3.5 transition-all"
            :label="__('general.input.first_name')"
            :placeholder="__('general.input.first_name_placeholder')" 
            wire:model="first_name" required />

        <x-form.input name="last_name" type="text" 
            class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl !py-3.5 transition-all"
            :label="__('general.input.last_name')"
            :placeholder="__('general.input.last_name_placeholder')" 
            wire:model="last_name" required />

        <x-form.input name="email" type="email" 
            class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl !py-3.5 transition-all"
            :label="__('general.input.email')"
            :placeholder="__('general.input.email_placeholder')" 
            required wire:model="email" divClass="col-span-2" />

        <x-form.input name="password" type="password" 
            class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl !py-3.5 transition-all"
            :label="__('general.input.password')" 
            :placeholder="__('general.input.password_placeholder')"
            wire:model="password" required />

        <x-form.input name="password_confirm" type="password" 
            class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl !py-3.5 transition-all"
            :label="__('general.input.password_confirmation')"
            :placeholder="__('general.input.password_confirmation_placeholder')" 
            wire:model="password_confirmation" required />

        <div class="col-span-2">
            <x-form.properties :custom_properties="$custom_properties" :properties="$properties" />
        </div>
    
        @if(config('settings.tos'))
            <div class="col-span-2 bg-gray-50 dark:bg-gray-800/40 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-primary-500/30 transition-all mt-2 shadow-sm">
                <x-form.checkbox wire:model="tos" name="tos" required>
                    <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 leading-relaxed uppercase tracking-widest">
                        {{ __('product.tos') }}
                        <a href="{{ config('settings.tos') }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:text-primary-500 transition-colors underline decoration-primary-500/30 underline-offset-4">
                            {{ __('product.tos_link') }}
                        </a>
                    </span>
                </x-form.checkbox>
            </div>
        @endif    
    </div>

    <x-captcha :form="'register'" />

    <div class="mt-4 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-700">
        <x-button.primary class="w-full !py-4 text-xs font-black uppercase tracking-[0.3em] shadow-lg shadow-primary-500/20 hover:scale-[1.01] active:scale-[0.99] transition-all" type="submit">
            {{ __('auth.sign_up') }}
        </x-button.primary>
    </div>

    {{-- Footer --}}
    <div class="text-center mt-8 pt-8 border-t border-gray-100 dark:border-gray-800 animate-in fade-in duration-1000 delay-[1000ms]">
        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">
            {{ __('auth.already_have_account') }}
            <a class="ml-2 font-black text-primary-600 dark:text-primary-400 hover:text-primary-500 transition-colors" href="{{ route('login') }}" wire:navigate>
                {{ __('auth.sign_in') }}
            </a>
        </p>
    </div>
</form>