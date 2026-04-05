<div class="relative min-h-screen bg-[#fdfaf5] dark:bg-[#1a120c] text-[#2d241e] dark:text-[#e8e2db] antialiased selection:bg-[#ff8c5a]/30 overflow-x-hidden font-sans">
    <style>
        :root {
            --color-warm-orange: #ff9d00;
            --color-warm-red: #f77a52;
            --color-warm-orange-dark: #ffb347;
            --color-warm-red-dark: #ff8c5a;
            --azel-glass: rgba(255, 255, 255, 0.7);
            --azel-glass-dark: rgba(26, 18, 12, 0.7);
        }

        /* Animated Mesh Gradient Background */
        .mesh-container {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: 
                radial-gradient(at 0% 0%, rgba(255, 189, 105, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(130, 221, 130, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(247, 122, 82, 0.08) 0px, transparent 50%);
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }

        .dark .mesh-container {
            background: 
                radial-gradient(at 0% 0%, rgba(255, 157, 0, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(247, 122, 82, 0.1) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(130, 221, 130, 0.05) 0px, transparent 50%);
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--color-warm-orange), var(--color-warm-red));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .dark .text-gradient {
            background: linear-gradient(135deg, var(--color-warm-orange-dark), var(--color-warm-red-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .premium-card {
            background: var(--azel-glass);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 40px;
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 20px 40px -15px rgba(139, 92, 45, 0.05);
        }

        .dark .premium-card {
            background: var(--azel-glass-dark);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.3);
        }

        .premium-card:hover {
            transform: translateY(-12px) scale(1.01);
            background: #ffffff;
            box-shadow: 0 50px 80px -20px rgba(139, 92, 45, 0.15);
            border-color: rgba(255, 157, 0, 0.3);
        }

        .dark .premium-card:hover {
            background: rgba(45, 36, 30, 0.9);
            box-shadow: 0 50px 80px -20px rgba(255, 157, 0, 0.2);
            border-color: rgba(255, 157, 0, 0.4);
        }

        .btn-main {
            background: #2d241e;
            color: white;
            transition: all 0.3s ease;
        }

        .dark .btn-main {
            background: linear-gradient(135deg, #ff9d00, #f77a52);
            color: #1a120c;
        }

        .btn-main:hover {
            background: var(--color-warm-orange);
            box-shadow: 0 10px 20px -5px rgba(255, 157, 0, 0.4);
            transform: translateY(-2px);
        }

        .dark .btn-main:hover {
            background: linear-gradient(135deg, #ffb347, #ff8c5a);
            box-shadow: 0 10px 20px -5px rgba(255, 157, 0, 0.5);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
        .animate-slide-up { animation: slide-up 0.8s ease-out forwards; }
        .animate-fade-in { animation: fade-in 1s ease-out forwards; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #fdfaf5; }
        .dark ::-webkit-scrollbar-track { background: #1a120c; }
        ::-webkit-scrollbar-thumb { background: #ff9d00; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #f77a52; }
        
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>

    <div class="mesh-container"></div>

    {{-- Hero Section (Without Top Bar) --}}
    <header class="relative z-10 pt-32 pb-24 lg:pt-40 lg:pb-32 text-center">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="inline-flex items-center gap-3 mb-8 px-4 py-2 bg-white/40 dark:bg-black/40 backdrop-blur-md rounded-full border border-white/60 dark:border-white/10 shadow-sm animate-float">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">⚡ Global Network Active</span>
            </div>

            <h1 class="text-5xl sm:text-7xl md:text-8xl lg:text-9xl font-black tracking-tighter leading-[0.9] sm:leading-[0.85] mb-8 sm:mb-10 animate-slide-up">
                Server Hosting <br>
                <span class="text-gradient italic">Redefined.</span>
            </h1>
            
            <p class="max-w-xl mx-auto text-gray-500/80 dark:text-gray-400 font-medium text-base sm:text-lg md:text-xl leading-relaxed animate-slide-up" style="animation-delay: 0.2s">
                {!! theme('home_page_text', 'High-performance cloud infrastructure designed for the next generation of builders.') !!}
            </p>
            
            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-10 animate-slide-up" style="animation-delay: 0.4s">
                <a href="{{ route('register') }}" class="btn-main px-8 py-4 rounded-full text-sm font-black uppercase tracking-[0.2em] shadow-2xl inline-flex items-center gap-2 group">
                    Get Started Now
                    <x-ri-arrow-right-line class="size-4 group-hover:translate-x-1 transition-transform" />
                </a>
                <a href="#services" class="px-8 py-4 rounded-full text-sm font-black uppercase tracking-[0.2em] border border-gray-300 dark:border-gray-700 hover:border-orange-500 dark:hover:border-orange-500 transition-all inline-flex items-center gap-2">
                    <x-ri-eye-line class="size-4" />
                    View Services
                </a>
            </div>
            
            {{-- Scroll Indicator --}}
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce hidden lg:block">
                <a href="#services" class="flex flex-col items-center gap-2 text-gray-400 hover:text-orange-500 transition-colors">
                    <span class="text-[8px] font-black uppercase tracking-wider">Scroll</span>
                    <x-ri-arrow-down-s-line class="size-5" />
                </a>
            </div>
        </div>
    </header>

    {{-- Service Grid --}}
    <main id="services" class="container mx-auto px-4 sm:px-6 pb-32 lg:pb-40 relative z-10">
        
        <div class="flex items-center gap-4 mb-12 lg:mb-16 px-4">
            <div class="w-8 h-px bg-gradient-to-r from-transparent to-orange-500"></div>
            <h2 class="text-xs font-black uppercase tracking-[0.4em] text-gray-400 dark:text-gray-500">
                🚀 Available Infrastructure
            </h2>
            <div class="h-px flex-grow bg-gradient-to-r from-orange-500 to-transparent"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-10">
            
            @foreach ($categories as $index => $category)
            <div 
                class="premium-card group flex flex-col h-full overflow-hidden animate-slide-up"
                style="animation-delay: {{ $index * 0.1 }}s"
            >
                {{-- Category Tag --}}
                <div class="px-6 sm:px-8 lg:px-10 pt-6 sm:pt-8 lg:pt-10 flex justify-between items-start">
                    <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 bg-[#2d241e] dark:bg-gradient-to-r dark:from-[#ff9d00] dark:to-[#f77a52] text-white dark:text-[#1a120c] rounded-full">
                        Node_0{{ $index + 1 }}
                    </span>
                    <x-ri-external-link-line class="size-4 text-gray-300 dark:text-gray-600 group-hover:text-orange-500 dark:group-hover:text-orange-400 transition-colors" />
                </div>

                <div class="p-6 sm:p-8 lg:p-10 flex flex-col flex-grow">

                    {{-- Image Area --}}
                    <div class="relative w-full aspect-[4/3] overflow-hidden bg-white/50 dark:bg-black/30 rounded-2xl lg:rounded-3xl mb-6 lg:mb-8 border border-white/80 dark:border-white/5 group-hover:bg-white dark:group-hover:bg-gray-800 transition-all duration-500 shadow-inner">
                        @if ($category->image)
                            <img 
                                src="{{ Storage::url($category->image) }}"
                                class="absolute inset-0 w-full h-full object-cover scale-105 group-hover:scale-110 transition-transform duration-700"
                                alt="{{ $category->name }}"
                            >
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <x-ri-server-line class="size-12 lg:size-16 text-orange-200 dark:text-orange-800 group-hover:text-orange-500 transition-colors" />
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-orange-500/0 to-orange-500/0 group-hover:from-orange-500/10 transition-all duration-500"></div>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-xl lg:text-2xl font-black tracking-tighter mb-3 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                        {{ $category->name }}
                    </h3>

                    {{-- Subtitle --}}
                    <p class="text-[10px] lg:text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest mb-6 lg:mb-8">
                        ⚡ Instant Delivery • 99.9% Uptime
                    </p>

                    {{-- Description --}}
                    @if($category->description)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6 line-clamp-2">
                            {{ Str::limit(strip_tags($category->description), 80) }}
                        </p>
                    @endif

                    {{-- Button --}}
                    <div class="mt-auto">
                        <a 
                            href="{{ route('category.show', ['category' => $category->slug]) }}"
                            wire:navigate
                            class="w-full py-4 lg:py-5 rounded-xl lg:rounded-2xl bg-white dark:bg-gray-800 text-[#2d241e] dark:text-white border border-gray-100 dark:border-gray-700 text-[10px] font-black uppercase tracking-[0.2em] flex items-center justify-center gap-2 hover:bg-[#2d241e] dark:hover:bg-[#ff9d00] hover:text-white dark:hover:text-[#1a120c] transition-all shadow-sm active:scale-95"
                        >
                            Browse Products
                            <x-ri-arrow-right-line class="size-3 lg:size-4" />
                        </a>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </main>

    {{-- Features Section --}}
    <section class="container mx-auto px-4 sm:px-6 py-16 lg:py-24 relative z-10">
        <div class="text-center mb-12 lg:mb-16">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tighter mb-4">Why Choose Us?</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">Enterprise-grade infrastructure with developer-friendly pricing</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
            <div class="text-center p-6 rounded-2xl bg-white/30 dark:bg-black/30 backdrop-blur-sm border border-gray-200/50 dark:border-gray-800/50 hover:border-orange-500/50 transition-all duration-300">
                <div class="w-16 h-16 mx-auto mb-4 bg-orange-500/10 rounded-2xl flex items-center justify-center">
                    <x-ri-flashlight-line class="size-8 text-orange-500" />
                </div>
                <h3 class="text-lg font-black mb-2">Lightning Fast</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">NVMe SSD storage and 10Gbps network for blazing fast performance</p>
            </div>
            
            <div class="text-center p-6 rounded-2xl bg-white/30 dark:bg-black/30 backdrop-blur-sm border border-gray-200/50 dark:border-gray-800/50 hover:border-orange-500/50 transition-all duration-300">
                <div class="w-16 h-16 mx-auto mb-4 bg-orange-500/10 rounded-2xl flex items-center justify-center">
                    <x-ri-shield-check-line class="size-8 text-orange-500" />
                </div>
                <h3 class="text-lg font-black mb-2">Secure by Default</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">DDoS protection, SSL certificates, and automated backups included</p>
            </div>
            
            <div class="text-center p-6 rounded-2xl bg-white/30 dark:bg-black/30 backdrop-blur-sm border border-gray-200/50 dark:border-gray-800/50 hover:border-orange-500/50 transition-all duration-300">
                <div class="w-16 h-16 mx-auto mb-4 bg-orange-500/10 rounded-2xl flex items-center justify-center">
                    <x-ri-customer-service-line class="size-8 text-orange-500" />
                </div>
                <h3 class="text-lg font-black mb-2">24/7 Expert Support</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Our team of experts is always ready to help you succeed</p>
            </div>
        </div>
    </section>

    {{-- Statistics Section --}}
    <section class="container mx-auto px-4 sm:px-6 pb-16 lg:pb-24 relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-10">
            <div class="text-center p-6 rounded-2xl bg-white/30 dark:bg-black/30 backdrop-blur-sm border border-gray-200/50 dark:border-gray-800/50">
                <div class="text-3xl lg:text-4xl font-black text-orange-500 mb-2">99.9%</div>
                <div class="text-[10px] lg:text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Uptime Guarantee</div>
            </div>
            <div class="text-center p-6 rounded-2xl bg-white/30 dark:bg-black/30 backdrop-blur-sm border border-gray-200/50 dark:border-gray-800/50">
                <div class="text-3xl lg:text-4xl font-black text-orange-500 mb-2">24/7</div>
                <div class="text-[10px] lg:text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Support Available</div>
            </div>
            <div class="text-center p-6 rounded-2xl bg-white/30 dark:bg-black/30 backdrop-blur-sm border border-gray-200/50 dark:border-gray-800/50">
                <div class="text-3xl lg:text-4xl font-black text-orange-500 mb-2">5K+</div>
                <div class="text-[10px] lg:text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Happy Customers</div>
            </div>
            <div class="text-center p-6 rounded-2xl bg-white/30 dark:bg-black/30 backdrop-blur-sm border border-gray-200/50 dark:border-gray-800/50">
                <div class="text-3xl lg:text-4xl font-black text-orange-500 mb-2">Instant</div>
                <div class="text-[10px] lg:text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Deployment</div>
            </div>
        </div>
    </section>


    {{-- CTA Section --}}
    <section class="container mx-auto px-4 sm:px-6 pb-32 lg:pb-40 relative z-10">
        <div class="premium-card p-8 sm:p-12 lg:p-20 flex flex-col lg:flex-row items-center justify-between gap-8 lg:gap-10 bg-gradient-to-br from-white to-orange-50/30 dark:from-gray-900 dark:to-orange-950/20">
            <div class="max-w-lg text-center lg:text-left">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tighter mb-4 lg:mb-6">Ready to scale your next project?</h2>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Join 5,000+ developers deploying on our authorized infrastructure.</p>
            </div>
            <a href="{{ route('register') }}" class="btn-main px-8 lg:px-10 py-4 lg:py-5 rounded-full text-xs font-black uppercase tracking-[0.3em] shadow-2xl inline-flex items-center gap-2 group">
                Get Started Instantly
                <x-ri-arrow-right-line class="size-4 group-hover:translate-x-1 transition-transform" />
            </a>
        </div>
    </section>

    {!! hook('pages.home') !!}
</div>