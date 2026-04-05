<x-app-layout>
    <x-slot name="title">
        {{ __('errors.500.title') }}
    </x-slot>

    <div class="relative min-h-[70vh] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
            <div class="absolute w-[500px] h-[500px] bg-warning/10 blur-[120px] rounded-full animate-pulse"></div>
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-30 mix-blend-soft-light"></div>
        </div>

        <div class="container relative z-10 flex flex-col items-center justify-center text-center py-20 px-6">
            <div class="flex items-center gap-3 mb-8 px-4 py-1.5 rounded-xl border border-warning/30 bg-warning/5 animate-bounce duration-[2000ms]">
                <x-ri-alert-fill class="size-4 text-warning shadow-[0_0_10px_rgba(245,158,11,0.5)]" />
                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-warning">System_Core_Instability_Detected</p>
            </div>

            <p class="text-xs font-black uppercase tracking-[0.5em] text-warning/60 mb-2">Protocol_500</p>
            
            <h1 class="text-6xl md:text-8xl font-black uppercase tracking-tighter text-base mb-6 drop-shadow-2xl">
                {{ __('errors.500.title') }}
            </h1>

            <div class="max-w-xl mx-auto space-y-6">
                <p class="text-sm md:text-lg font-bold text-base/40 leading-relaxed uppercase tracking-wide px-4">
                    {{ __('errors.500.message') }}
                </p>

                <div class="bg-black/40 border border-white/5 rounded-2xl p-6 font-mono text-[10px] text-left space-y-1 opacity-60 overflow-hidden relative">
                    <div class="flex justify-between border-b border-white/5 pb-2 mb-3">
                        <span class="text-warning/80">STACK_TRACE_SNAPSHOT</span>
                        <span class="text-base/20">LOG_ID: #{{ str(rand(100000, 999999)) }}</span>
                    </div>
                    <p class="text-error/60">> Critical Exception: Logic_Loop_Failure</p>
                    <p class="text-base/30">> Attempting automated core recovery...</p>
                    <p class="text-base/30">> Memory allocation at 0x00FF42 failed.</p>
                    <p class="text-success/50">> Safety protocols engaged.</p>
                    
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/5 to-transparent h-20 w-full animate-scanline"></div>
                </div>
            </div>

            <div class="mt-12 flex flex-col items-center gap-4">
                <a href="{{ url()->previous() }}" class="group relative">
                    <x-button.secondary class="!rounded-xl !py-4 !px-10 !text-[11px] !font-black !uppercase !tracking-[0.3em] !bg-white/5 !border-white/10 hover:!bg-white/10 transition-all">
                        <x-ri-refresh-line class="size-4 mr-2 group-hover:rotate-180 transition-transform duration-500" />
                        Attempt_Manual_Reboot
                    </x-button.secondary>
                </a>
                
                <a href="{{ route('home') }}" wire:navigate class="text-[10px] font-black uppercase tracking-[0.2em] text-base/20 hover:text-primary transition-colors">
                    // Return_To_Safe_Node
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes scanline {
        from { transform: translateY(-100%); }
        to { transform: translateY(200%); }
    }
    .animate-scanline {
        animation: scanline 3s linear infinite;
    }
</style>
</x-app-layout>