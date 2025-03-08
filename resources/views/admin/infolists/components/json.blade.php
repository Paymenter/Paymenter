<div class="fi-in-entry-wrp">
    <div class="grid gap-y-2">
        <div class="flex items-center gap-x-3 justify-between">
            <dt class="fi-in-entry-wrp-label inline-flex items-center gap-x-3">
                <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    {{ $entry->getLabel() }}
                </span>
            </dt>
        </div>
        <pre style="overflow-x: auto;"><code>{{ json_encode($getState(), JSON_PRETTY_PRINT) }}</code></pre>
    </div>
</div>