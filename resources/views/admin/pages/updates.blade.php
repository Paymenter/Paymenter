<x-filament-panels::page>
    @assets
    <script src="{{ asset('js/ansi_up/ansi_up.min.js') }}"></script>
    @endassets
    @if(config('app.version') == 'beta')
    <div>
        <strong>Warning:</strong> You are using a beta version of the application. This can cause problems
    </div>
    @endif


    @if(config('app.version') == 'beta' && config('settings.latest_commit') != config('app.commit'))
    <div class="flex flex-col gap-1">
        <div>
            <strong>Latest commit:</strong> {{ config('settings.latest_commit') }}
        </div>
        <div>
            <strong>Your commit:</strong> {{ config('app.commit') }}
        </div>
        <p>See <a
                href="https://paymenter.org/docs/installation/updating">https://paymenter.org/docs/installation/updating</a>
            on how to update</p>

        <p class="mt-2">OR try the web updater (This is a beta feature, use at your own risk)</p>
        <div class="mt-2">
            {{ $this->update }}
        </div>
    </div>
    @elseif(config('app.version') != config('settings.latest_version') && config('app.version') != 'beta')
    <div class="flex flex-col gap-1">
        <div>
            <strong>Latest version:</strong> {{ config('settings.latest_version') }}
        </div>
        <div>
            <strong>Your version:</strong> {{ config('app.version') }}
        </div>
        <p>See <a
                href="https://paymenter.org/docs/installation/updating">https://paymenter.org/docs/installation/updating</a>
            on how to update</p>

        <p class="mt-2">OR try the web updater (This is a beta feature, use at your own risk)</p>
        <div class="mt-2">
            {{ $this->update }}
        </div>
    </div>
    @else
    <div class="flex flex-col gap-1">
        <div>
            <strong>Latest version:</strong> {{ config('settings.latest_version') }}
        </div>
        <div>
            <strong>Your version:</strong> {{ config('app.version') }}
        </div>
        <p>You are up to date!</p>
    </div>
    @endif
    <code>
        <pre id="update-result" class="mt-2" x-data="{ output: '' }" x-html="output"  x-on:update-completed.window="output = (new AnsiUp()).ansi_to_html($event.detail[0].output);">
        </pre>
    </code>
</x-filament-panels::page>
