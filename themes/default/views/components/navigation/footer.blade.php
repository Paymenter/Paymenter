<footer class="w-full bg-white dark:bg-[#0b1120] border-t border-gray-200 dark:border-gray-800/60 mt-auto">
    <div class="container mx-auto px-6 py-16">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            
            <div class="space-y-6">
                <div class="flex items-center gap-3 group cursor-pointer">
                    <div class="p-2 bg-orange-500/10 rounded-xl group-hover:bg-orange-500/20 transition-colors">
                        <x-logo class="h-7 w-auto" />
                    </div>
                    <span class="text-xl font-black tracking-tight text-gray-900 dark:text-white">
                        {{ config('app.name') }}
                    </span>
                </div>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-xs">
                    Next-generation cloud infrastructure built for speed, security, and global scale.
                </p>
                
                <div class="flex gap-3">
                    @php
                        $socials = [
                            ['icon' => 'ri-discord-fill', 'link' => config('custom.socials.discord'), 'color' => 'hover:bg-indigo-500'],
                            ['icon' => 'ri-github-fill', 'link' => config('custom.socials.github'), 'color' => 'hover:bg-black'],
                            ['icon' => 'ri-twitter-x-fill', 'link' => config('custom.socials.twitter'), 'color' => 'hover:bg-blue-400'],
                        ];
                    @endphp

                    @foreach($socials as $social)
                        @if($social['link'])
                            <a href="{{ $social['link'] }}" target="_blank" 
                               class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 {{ $social['color'] }} hover:text-white transition-all duration-300 hover:-translate-y-1">
                                <x-dynamic-component :component="$social['icon']" class="size-5" />
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
            
            <div>
                <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-6">
                    Resources
                </h3>
                <ul class="space-y-4">
                    @foreach(config('custom.pages') as $page)
                        @if($page['name'] && $page['link'])
                            <li>
                                <a href="{{ $page['link'] }}" 
                                   class="text-sm text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 transition-colors flex items-center group">
                                    <span class="w-0 group-hover:w-4 transition-all duration-300 overflow-hidden text-orange-500">→</span>
                                    {{ $page['name'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            
            <div>
                <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-6">
                    Legal & Trust
                </h3>
                <ul class="space-y-4">
                    @foreach(['terms' => 'Terms of Service', 'privacy' => 'Privacy Policy', 'cookies' => 'Cookie Policy'] as $route => $label)
                        <li>
                            <a href="{{ Route::has($route) ? route($route) : '#' }}" 
                               class="text-sm text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 transition-colors">
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-800/30 p-6 rounded-2xl border border-gray-100 dark:border-gray-700/50">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">Stay Updated</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Get the latest feature updates and server drops.</p>
                <form class="flex gap-2">
                    <input type="email" placeholder="email@domain.com" 
                           class="flex-1 px-4 py-2.5 text-xs bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all">
                    <button class="p-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-lg shadow-orange-500/20 transition-all active:scale-95">
                        <x-ri-send-plane-2-line class="size-4" />
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mt-16 pt-8 border-t border-gray-100 dark:border-gray-800/60 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-xs text-gray-400">
                © {{ date('Y') }} {{ config('app.name') }}. All systems operational.
            </p>
            
            <div class="flex items-center gap-6">
                <div class="flex gap-4 grayscale opacity-50 hover:opacity-100 transition-opacity">
                    <x-ri-visa-line class="h-5 w-auto text-gray-600 dark:text-gray-400" />
                    <x-ri-mastercard-line class="h-5 w-auto text-gray-600 dark:text-gray-400" />
                    <x-ri-paypal-line class="h-5 w-auto text-gray-600 dark:text-gray-400" />
                </div>
                <div class="h-4 w-[1px] bg-gray-200 dark:bg-gray-800"></div>
                <a href="https://paymenter.org/" class="text-xs font-medium text-gray-500 hover:text-orange-500 transition-colors">
                    Powered by <span class="text-gray-900 dark:text-white">Paymenter</span>
                </a>
            </div>
        </div>
        
    </div>
</footer>