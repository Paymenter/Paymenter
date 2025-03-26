<x-filament-panels::page>

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
        <p>See <a href="https://v1.paymenter.org/docs/installation/updating">https://v1.paymenter.org/docs/installation/updating</a> on how to update</p>


        <p class="mt-2">OR try the web updater (This is a beta feature, use at your own risk)</p>
        <div class="mt-2">
            {{  $this->update }}

            {{ $output }}
        </div>


    </div>
    @elseif(config('app.version') != config('settings.latest_version'))
    <div class="flex flex-col gap-1">
        <div>
            <strong>Latest version:</strong> {{ config('settings.latest_version') }}
        </div>
        <div>
            <strong>Your version:</strong> {{ config('app.version') }}
        </div>
        <p>See <a href="https://v1.paymenter.org/docs/installation/updating">https://v1.paymenter.org/docs/installation/updating</a> on how to update</p>
        {{-- <p class="mt-2">OR try the web updater (This is a beta feature, use at your own risk)</p>
        <div class="mt-2">
            {{  $this->update }}
        </div> --}}
    </div>
    @endif
</x-filament-panels::page>