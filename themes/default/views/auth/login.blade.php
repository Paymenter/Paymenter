<form 
    class="mx-auto flex flex-col gap-6 mt-16 px-8 sm:px-16 pb-12 bg-white dark:bg-gray-900/50 backdrop-blur-sm rounded-[2.5rem] border border-gray-200 dark:border-gray-800 shadow-2xl shadow-primary-500/5 xl:max-w-[35%] w-full animate-in fade-in zoom-in-95 duration-700" 
    wire:submit="submit" 
    id="login">

    {{-- Header Section --}}
    <div class="flex flex-col items-center mt-12 mb-8 animate-in slide-in-from-top-4 duration-1000 delay-100">
        <div class="relative group mb-6">
            <div class="absolute -inset-1 bg-gradient-to-r from-primary-500 to-brand-teal rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
            <x-logo class="relative h-12" />
        </div>
        
        <h1 class="text-3xl font-black tracking-tighter bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent uppercase">
            {{ __('auth.sign_in_title') }}
        </h1>
        <div class="w-12 h-1 bg-primary-500 rounded-full mt-3"></div>
    </div>

    {{-- Inputs Section --}}
    <div class="space-y-5 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-300">
        <div class="group">
            <x-form.input name="email" type="email" 
                class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl !py-3.5 transition-all"
                :label="__('general.input.email')"
                :placeholder="__('general.input.email_placeholder')" 
                wire:model="email" hideRequiredIndicator required />
        </div>

        <div class="group">
            <x-form.input name="password" type="password" 
                class="!bg-gray-50 dark:!bg-gray-800/50 !border-gray-200 dark:!border-gray-700 focus:!border-primary-500 !rounded-xl !py-3.5 transition-all"
                :label="__('general.input.password')"
                :placeholder="__('general.input.password_placeholder')" 
                required hideRequiredIndicator wire:model="password" />
        </div>
    </div>

    {{-- Actions Section --}}
    <div class="flex items-center justify-between animate-in fade-in duration-700 delay-500">
        <x-form.checkbox name="remember" label="Stay logged in" wire:model="remember" 
            class="text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400" />
        
        <a class="text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-widest hover:text-primary-500 transition-colors"
            href="{{ route('password.request') }}">
            {{ __('auth.forgot_password') }}
        </a>
    </div>

    <x-captcha :form="'login'" />

    <div class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-700">
        <x-button.primary class="w-full !py-4 text-xs font-black uppercase tracking-[0.3em] shadow-lg shadow-primary-500/20 hover:scale-[1.02] active:scale-[0.98] transition-all" type="submit">
            {{ __('auth.sign_in') }}
        </x-button.primary>
    </div>

    {{-- OAuth Section --}}
    @if (config('settings.oauth_github') || config('settings.oauth_google') || config('settings.oauth_discord'))
    <div class="flex flex-col items-center mt-6 animate-in fade-in duration-1000 delay-1000">
        <div class="my-6 flex items-center w-full gap-4">
            <span aria-hidden="true" class="h-px grow bg-gradient-to-r from-transparent to-gray-200 dark:to-gray-800"></span>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500">
                {{ __('auth.or_sign_in_with') }}
            </span>
            <span aria-hidden="true" class="h-px grow bg-gradient-to-l from-transparent to-gray-200 dark:to-gray-800"></span>
        </div>
        
        <div class="flex flex-row flex-wrap justify-center gap-4">
            @foreach (['github', 'google', 'discord'] as $provider)
                @if (config('settings.oauth_' . $provider))
                <a href="{{ route('oauth.redirect', $provider) }}"
                    class="group flex items-center justify-center size-12 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-primary-500 hover:shadow-lg hover:shadow-primary-500/10 transition-all duration-300">
                    <img src="/assets/images/{{ $provider }}-dark.svg" alt="{{ $provider }}"
                        class="size-5 grayscale opacity-70 group-hover:grayscale-0 group-hover:opacity-100 transition-all group-hover:scale-110">
                </a>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Footer --}}
    @if(!config('settings.registration_disabled', false))
    <div class="text-center mt-10 pt-8 border-t border-gray-100 dark:border-gray-800 animate-in fade-in duration-1000 delay-[1200ms]">
        <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
            {{ __('auth.dont_have_account') }}
            <a class="ml-2 font-black text-primary-600 dark:text-primary-400 hover:text-primary-500 transition-colors" href="{{ route('register') }}" wire:navigate>
                {{ __('auth.sign_up') }}
            </a>
        </p>
    </div>
    @endif
</form>