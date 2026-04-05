<x-app-layout>
    <x-slot name="title">
        {{ __('errors.404.title') }}
    </x-slot>

    <div class="relative min-h-[70vh] flex items-center justify-center overflow-hidden">
        {{-- Background Effects --}}
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none overflow-hidden">
            <span class="text-[30vw] font-black text-primary opacity-[0.03] blur-3xl animate-pulse">404</span>
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-overlay"></div>
            
            {{-- Animated Grid Background --}}
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:40px_40px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
            
            {{-- Floating Particles --}}
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-primary/20 rounded-full animate-float" style="animation-delay: 0s"></div>
                <div class="absolute top-1/3 right-1/4 w-1.5 h-1.5 bg-primary/30 rounded-full animate-float" style="animation-delay: 1s"></div>
                <div class="absolute bottom-1/4 left-1/3 w-2.5 h-2.5 bg-primary/25 rounded-full animate-float" style="animation-delay: 2s"></div>
                <div class="absolute bottom-1/3 right-1/3 w-1 h-1 bg-primary/35 rounded-full animate-float" style="animation-delay: 1.5s"></div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="container relative z-10 flex flex-col items-center justify-center text-center py-20 px-4 sm:px-6">
            
            {{-- Status Badge --}}
            <div class="flex items-center gap-3 mb-8 px-4 py-1.5 rounded-full border border-red-500/20 bg-red-500/5 backdrop-blur-sm animate-in fade-in slide-in-from-top-4 duration-700">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-red-500">Link_Integrity_Compromised</p>
            </div>

            {{-- Error Protocol --}}
            <p class="text-xs font-black uppercase tracking-[0.5em] text-primary-600 dark:text-primary-400 mb-2 drop-shadow-[0_0_8px_rgba(var(--primary-rgb),0.5)] animate-pulse">
                Error_Protocol_0xDEADBEEF
            </p>
            
            {{-- 404 Title --}}
            <h1 class="text-7xl md:text-9xl font-black uppercase tracking-tighter bg-gradient-to-r from-gray-900 via-gray-700 to-gray-900 dark:from-white dark:via-gray-300 dark:to-white bg-clip-text text-transparent mb-4 animate-in zoom-in duration-1000">
                404
            </h1>

            {{-- Error Message --}}
            <div class="max-w-lg mx-auto">
                <p class="text-base md:text-xl font-bold text-gray-600 dark:text-gray-400 leading-relaxed uppercase tracking-wide">
                    {{ __('errors.404.message') ?: 'The page you are looking for does not exist or has been moved.' }}
                </p>
                
                {{-- Tech Details --}}
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3 text-[10px] font-mono text-gray-500 dark:text-gray-500 uppercase tracking-widest">
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded">Target: Null</span>
                    <span class="hidden sm:inline text-gray-400">•</span>
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded">Status: 404 Not Found</span>
                    <span class="hidden sm:inline text-gray-400">•</span>
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded">Loc: 0xDEADBEEF</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4 animate-in fade-in slide-in-from-bottom-8 duration-1000">
                <a href="{{ route('home') }}" wire:navigate class="group relative">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-primary-600 to-primary-400 rounded-xl blur opacity-30 group-hover:opacity-60 transition duration-300"></div>
                    
                    <x-button.primary class="relative rounded-xl px-6 py-3 text-sm font-semibold uppercase tracking-wide shadow-lg transition-all active:scale-95">
                        <x-ri-home-4-line class="size-4 mr-2" />
                        {{ __('errors.404.return_home') ?: 'Return to Home' }}
                    </x-button.primary>
                </a>
                
                <a href="javascript:history.back()" class="group relative">
                    <x-button.secondary class="rounded-xl px-6 py-3 text-sm font-semibold uppercase tracking-wide transition-all active:scale-95">
                        <x-ri-arrow-go-back-line class="size-4 mr-2" />
                        {{ __('errors.404.go_back') ?: 'Go Back' }}
                    </x-button.secondary>
                </a>
            </div>

            {{-- Footer Scan Text --}}
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-4 opacity-30">
                <div class="h-px w-16 bg-gradient-to-l from-gray-400 to-transparent"></div>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span>
                    </span>
                    <span class="text-[8px] font-black uppercase tracking-[0.3em] text-gray-500">Scanning_For_Nodes</span>
                </div>
                <div class="h-px w-16 bg-gradient-to-r from-gray-400 to-transparent"></div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Additional CSS for custom animations --}}
@push('styles')
<style>
    @keyframes float {
        0%, 100% {
            transform: translateY(0px) translateX(0px);
            opacity: 0;
        }
        50% {
            transform: translateY(-20px) translateX(10px);
            opacity: 1;
        }
    }
    
    .animate-float {
        animation: float 4s ease-in-out infinite;
    }
    
    @keyframes glitch {
        0%, 100% {
            text-shadow: 2px 0 red, -2px 0 blue;
        }
        25% {
            text-shadow: -2px 0 red, 2px 0 blue;
        }
        50% {
            text-shadow: 2px 0 red, -2px 0 blue;
        }
        75% {
            text-shadow: -2px 0 red, 2px 0 blue;
        }
    }
    
    .animate-glitch {
        animation: glitch 0.3s ease-in-out infinite;
    }
</style>
@endpush