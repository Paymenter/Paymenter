<div>
    {{-- Hero --}}
    <section class="relative overflow-hidden pt-32 pb-20 px-4">
        {{-- Background glow orbs --}}
        <div class="absolute top-0 left-1/4 w-[600px] h-[400px] bg-primary/8 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute top-20 right-1/4 w-[400px] h-[300px] bg-secondary/6 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative container mx-auto max-w-5xl text-center">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full
                        bg-primary/10 border border-primary/20 text-primary text-xs font-semibold
                        uppercase tracking-wider mb-6">
                <span class="h-1.5 w-1.5 rounded-full bg-primary animate-pulse"></span>
                3 Nodes Online · Dallas · Ashburn · LA
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight mb-6"
                style="font-family: 'Space Grotesk', sans-serif;">
                <span class="gradient-text">{{ config('app.name') }}</span>
                <br>
                <span class="text-base/90">Billing Portal</span>
            </h1>

            <div class="prose prose-sm dark:prose-invert max-w-2xl mx-auto text-muted mb-10">
                {!! Str::markdown(theme('home_page_text', 'Choose a plan and get connected in minutes.'), [
                    'allow_unsafe_links' => false,
                    'renderer' => ['soft_break' => '<br>']
                ]) !!}
            </div>

            @if(!auth()->check())
                <div class="flex items-center justify-center gap-4 flex-wrap">
                    <a href="{{ route('register') }}" wire:navigate>
                        <x-button.primary class="text-base px-8 py-3 text-sm">
                            Get Started
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </x-button.primary>
                    </a>
                    <a href="{{ route('login') }}" wire:navigate>
                        <x-button.secondary class="text-sm px-8 py-3">
                            Sign In
                        </x-button.secondary>
                    </a>
                </div>
            @else
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <x-button.primary class="text-sm px-8 py-3">
                        Go to Dashboard
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </x-button.primary>
                </a>
            @endif
        </div>
    </section>

    {{-- Trust bar --}}
    <div class="border-y border-neutral/30 py-4 px-4">
        <div class="container mx-auto max-w-4xl flex flex-wrap items-center justify-center gap-6 text-sm text-muted">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Zero-Log Policy
            </div>
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                WireGuard Protocol
            </div>
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                7-Day Refund Guarantee
            </div>
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                ChaCha20 Encryption
            </div>
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Cancel Anytime
            </div>
        </div>
    </div>

    {{-- Products / Categories --}}
    <section class="py-16 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-3" style="font-family: 'Space Grotesk', sans-serif;">
                    Choose Your <span class="gradient-text">Plan</span>
                </h2>
                <p class="text-muted max-w-xl mx-auto text-sm">
                    All plans include WireGuard protocol, zero-log policy, and DDoS mitigation.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5 mb-4">
                @foreach ($categories as $category)
                    <a href="{{ route('category.show', ['category' => $category->slug]) }}" wire:navigate
                       class="flex flex-col gap-3 rounded-2xl bg-background-secondary border border-neutral/50
                              hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5
                              p-5 transition-all group">
                        @if($category->image)
                            <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
                                 class="w-12 h-12 rounded-xl object-cover object-center">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center">
                                <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-base group-hover:text-primary transition-colors">{{ $category->name }}</h3>
                            @if(theme('show_category_description', true))
                                <div class="prose prose-sm dark:prose-invert mt-1 text-muted line-clamp-2">
                                    {!! $category->description !!}
                                </div>
                            @endif
                        </div>
                        <div class="mt-auto">
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-primary">
                                View Plans
                                <svg class="h-3 w-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {!! hook('pages.home') !!}

</div>
