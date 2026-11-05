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
                <div class="mt-2" x-data="{ running: false }" x-show="!running" x-on:start-update.window="running = true" x-on:update-failed.window="running = false">
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
                <div class="mt-2" x-data="{ running: false }" x-show="!running" x-on:start-update.window="running = true" x-on:update-failed.window="running = false">
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
                <pre id="update-result" class="mt-2" x-data="{
                    output: '',
                    async run() {
                        this.output = '';
                        let raw = '';
                        const ansi = new AnsiUp();
                        try {
                            const response = await fetch('{{ route('admin.updates.run') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '{{ csrf_token() }}',
                                    'Accept': 'text/plain',
                                },
                                credentials: 'same-origin',
                            });
                            if (!response.ok || !response.body) {
                                this.output = 'Update request failed (HTTP ' + response.status + ').';
                                window.dispatchEvent(new CustomEvent('update-failed'));
                                return;
                            }
                            const reader = response.body.getReader();
                            const decoder = new TextDecoder();
                            while (true) {
                                const { done, value } = await reader.read();
                                if (done) break;
                                raw += decoder.decode(value, { stream: true });
                                this.output = ansi.ansi_to_html(raw);
                            }
                        } catch (e) {
                            this.output = ansi.ansi_to_html(raw) + '\nConnection lost: ' + e.message;
                        }
                    }
                }" x-html="output" x-on:start-update.window="run()"></pre>
            </code>
        </x-filament::section>

        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>