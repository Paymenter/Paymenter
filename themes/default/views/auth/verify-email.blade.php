<div 
    class="mx-auto flex flex-col items-center gap-6 mt-20 px-8 sm:px-16 py-12 bg-white/70 backdrop-blur-xl rounded-[2.5rem] border border-neutral/20 shadow-2xl shadow-primary/5 xl:max-w-[45%] w-full animate-in fade-in zoom-in-95 duration-700">
    
    <div class="flex flex-col items-center mb-4 animate-in slide-in-from-top-4 duration-1000 delay-100">
        <div class="size-20 rounded-full bg-primary/10 flex items-center justify-center mb-6 relative">
            <x-ri-mail-send-line class="size-10 text-primary animate-pulse" />
            <span class="absolute top-0 right-0 size-4 bg-brand-teal rounded-full border-4 border-white"></span>
        </div>
        <h1 class="text-3xl font-black tracking-tighter text-base uppercase text-center">
            {{ __('auth.verification.notice') }}
        </h1>
        <div class="w-12 h-1 bg-primary rounded-full mt-2 opacity-20"></div>
    </div>

    <div class="text-center space-y-4 animate-in fade-in duration-700 delay-300">
        <p class="text-sm font-medium text-base/60 leading-relaxed max-w-sm">
            {{ __('auth.verification.check_your_email') }}
        </p>
    </div>

    <form class="flex flex-col gap-6 mt-4 w-full animate-in fade-in slide-in-from-bottom-4 duration-700 delay-500" 
          wire:submit.prevent="submit" 
          id="verify-email">
        
        <x-captcha :form="'verify-email'" />

        <div class="p-6 rounded-2xl bg-neutral/5 border border-neutral/10 text-center">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-base/40 mb-4">
                {{ __('auth.verification.not_received') }}
            </p>
            
            <x-button.primary class="w-full !py-4 text-xs font-black uppercase tracking-[0.3em] shadow-lg shadow-primary/10 hover:shadow-primary/20 transition-all" type="submit">
                {{ __('auth.verification.request_another') }}
            </x-button.primary>
        </div>
    </form>

    <div class="mt-4 animate-in fade-in duration-1000 delay-700">
        <a href="{{ route('logout') }}" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="text-[10px] font-black text-error uppercase tracking-widest hover:underline opacity-60 hover:opacity-100 transition-all">
            {{ __('auth.logout') }}
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</div>