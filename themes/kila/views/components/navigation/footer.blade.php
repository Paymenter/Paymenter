<footer class="w-full px-4 py-8 lg:mt-72 mt-44 bg-background-secondary border-t border-neutral">
    <div class="container mx-auto">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <!-- Logo Section -->
            <div class="flex items-center">
                <x-logo class="h-12" />
            </div>

            <!-- Links Section -->
            <div class="flex flex-wrap items-center justify-center gap-6 text-sm">
                <a href="{{ route('home') }}" class="text-muted hover:text-base transition-colors">
                    {{ __('Home') }}
                </a>
                @if(config('settings.tos_url'))
                <a href="{{ config('settings.tos_url') }}" class="text-muted hover:text-base transition-colors">
                    {{ __('Terms of Service') }}
                </a>
                @endif
                @if(config('settings.privacy_url'))
                <a href="{{ config('settings.privacy_url') }}" class="text-muted hover:text-base transition-colors">
                    {{ __('Privacy Policy') }}
                </a>
                @endif
            </div>

            <!-- Social Media Section -->
            <div class="flex items-center gap-4">
                @if(config('settings.discord_url'))
                <a href="{{ config('settings.discord_url') }}" target="_blank" rel="noopener noreferrer"
                   class="text-muted hover:text-primary transition-colors" aria-label="Discord">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                    </svg>
                </a>
                @endif
                @if(config('settings.twitter_url'))
                <a href="{{ config('settings.twitter_url') }}" target="_blank" rel="noopener noreferrer"
                   class="text-muted hover:text-primary transition-colors" aria-label="Twitter">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>

        <!-- Copyright and Paymenter Link -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-8 pt-6 border-t border-neutral">
            <div class="text-sm text-muted">
                {{ __('© :year :app_name. All rights reserved.', ['year' => date('Y'), 'app_name' => config('app.name')]) }}
            </div>

            {{-- Paymenter is free and opensource, removing this link is not cool --}}
            <a href="https://paymenter.org" target="_blank"
                class="group flex items-center gap-2 text-muted hover:text-base transition-colors">
                <svg class="size-4 text-current group-hover:text-primary" width="150" height="205" viewBox="0 0 150 205" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_1_17)">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0 107V205H42.8571V139.638H100C133.333 139.638 150 123 150 89.7246V69.5L75 107V69.5L148.227 32.8863C143.133 10.9621 127.057 0 100 0H0V107ZM0 107V69.5L75 32V69.5L0 107Z"></path>
                    </g>
                    <defs>
                        <clipPath id="clip0_1_17">
                            <rect width="150" height="205"></rect>
                        </clipPath>
                    </defs>
                </svg>
                <p class="text-sm">{{ __('Powered by Paymenter') }}</p>
            </a>
        </div>
    </div>
</footer>