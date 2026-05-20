<x-filament-panels::page>
    @assets
    <script src="{{ asset('js/ansi_up/ansi_up.min.js') }}"></script>
    @endassets
    @if(config('app.version') == 'beta')
    <div class="p-4 border border-warning-300 bg-warning-50 text-warning-800 rounded-lg dark:bg-warning-950/20 dark:border-warning-800/30 dark:text-warning-400">
        {!! __('updates.beta_warning') !!}
    </div>
    @endif


    @if(config('app.version') == 'beta' && config('settings.latest_commit') != config('app.commit'))
    <div class="flex flex-col gap-3">
        <div class="space-y-1">
            <div>
                <strong>{{ __('updates.latest_commit') }} :</strong> {{ config('settings.latest_commit') }}
            </div>
            <div>
                <strong>{{ __('updates.your_commit') }} :</strong> {{ config('app.commit') }}
            </div>
        </div>
        <p>{!! __('updates.updating_instructions', ['url' => 'https://paymenter.org/docs/installation/updating']) !!}</p>

        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('updates.web_updater_notice') }}</p>
        <div class="mt-2">
            {{ $this->update }}
        </div>
    </div>
    @elseif(config('app.version') != config('settings.latest_version') && config('app.version') != 'beta')
    <div class="flex flex-col gap-3">
        <div class="space-y-1">
            <div>
                <strong>{{ __('updates.latest_version') }} :</strong> {{ config('settings.latest_version') }}
            </div>
            <div>
                <strong>{{ __('updates.your_version') }} :</strong> {{ config('app.version') }}
            </div>
        </div>
        <p>{!! __('updates.updating_instructions', ['url' => 'https://paymenter.org/docs/installation/updating']) !!}</p>

        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('updates.web_updater_notice') }}</p>
        <div class="mt-2">
            {{ $this->update }}
        </div>
    </div>
    @else
    <div class="flex flex-col gap-1">
        <div>
            <strong>{{ __('updates.latest_version') }} :</strong> {{ config('settings.latest_version') }}
        </div>
        <div>
            <strong>{{ __('updates.your_version') }} :</strong> {{ config('app.version') }}
        </div>
        <p>{{ __('updates.up_to_date') }}</p>
    </div>
    @endif
    <code>
        <pre id="update-result" class="mt-2" x-data="{ output: '' }" x-html="output"  x-on:update-completed.window="output = (new AnsiUp()).ansi_to_html($event.detail[0].output);">
        </pre>
    </code>
</x-filament-panels::page>
