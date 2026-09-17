<x-filament-panels::page>
    @assets
    <script src="{{ asset('js/ansi_up/ansi_up.min.js') }}"></script>
    @endassets

    <div class="flex flex-col gap-6">
        <x-filament::section>
            <x-slot name="heading">
                System Updates
            </x-slot>

            @if(config('app.version') == 'beta')
            <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300"
                role="alert">
                <strong>Beta release:</strong> This version may contain unfinished features or unexpected issues.
            </div>
            @endif

            @if(config('app.version') == 'beta' && config('settings.latest_commit') != config('app.commit'))
            <div class="flex flex-col gap-2">
                <div>
                    <strong>Latest commit:</strong> {{ config('settings.latest_commit') }}
                </div>
                <div>
                    <strong>Your commit:</strong> {{ config('app.commit') }}
                </div>
                <p>Review the <a class="text-primary-600 underline" href="https://paymenter.org/docs/installation/updating"
                    target="_blank">update documentation</a> before continuing.</p>

                <p class="mt-2">Alternatively, use the web updater. This beta feature is provided at your own risk.</p>
                <div class="mt-2">
                    {{ $this->update }}
                </div>
            </div>
            @elseif(config('app.version') != config('settings.latest_version') && config('app.version') != 'beta')
            <div class="flex flex-col gap-2">
                <div>
                    <strong>Latest version:</strong> {{ config('settings.latest_version') }}
                </div>
                <div>
                    <strong>Your version:</strong> {{ config('app.version') }}
                </div>
                <p>Review the <a class="text-primary-600 underline" href="https://paymenter.org/docs/installation/updating"
                    target="_blank">update documentation</a> before continuing.</p>

                <p class="mt-2">Alternatively, use the web updater. Use at your own risk.</p>
                <div class="mt-2">
                    {{ $this->update }}
                </div>
            </div>
            @else
            <div class="flex flex-col gap-2">
                <div>
                    <strong>Latest version:</strong> {{ config('settings.latest_version') ?? config('app.version') }}
                </div>
                <div>
                    <strong>Your version:</strong> {{ config('app.version') }}
                </div>
                <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">You are up to date!</p>
            </div>
            @endif

            <code>
                <pre id="update-result" class="mt-2" x-data="{ output: '' }" x-html="output" x-on:update-completed.window="output = (new AnsiUp()).ansi_to_html($event.detail[0].output);"></pre>
            </code>
        </x-filament::section>

        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>