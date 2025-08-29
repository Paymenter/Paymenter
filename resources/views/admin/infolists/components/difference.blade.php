<div class="fi-in-entry-wrp">
    <div class="grid gap-y-2">
        <div class="flex items-center gap-x-3 justify-between">
            <dt class="fi-in-entry-wrp-label inline-flex items-center gap-x-3">
                <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                    {{ $entry->getLabel() }}
                </span>
            </dt>
        </div>

        <div class="flex-grow space-y-1">
            @if($record->event == 'created')
            <div class="text-sm text-gray-950 dark:text-white">
                @if(count($record->getModified()) === 0)
                <span class="text-xs text-gray-950 dark:text-white">
                    No changes
                </span>
                @else
                @foreach ($record->getModified() as $key => $value)
                @if(empty($value['new']))
                @continue
                @endif
                <div class="bg-background-secondary rounded">
                    <div class="flex flex-wrap items-center gap-x-2">
                        <span class="font-semibold">{{ $key }}</span>
                        <p class="text-green-300 p-1 rounded font-mono">
                            {{ $value['new'] ?? '' }}
                        </p>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
            @else
            <div class="text-sm text-gray-950 dark:text-white">
                @if(count($record->getModified()) === 0)
                <span class="text-xs text-gray-950 dark:text-white">
                    No changes
                </span>
                @else
                @foreach ($record->getModified() as $key => $value)
                @if(empty($value['new']) && empty($value['old']))
                @continue
                @endif
                <div class="bg-background-secondary rounded">
                    <div class="flex flex-wrap items-center gap-x-2">
                        <span class="font-semibold">{{ $key }}</span> changed from
                        <p class="text-red-300 p-1 rounded font-mono">
                            {{ $value['old'] ?? '' }}
                        </p>
                        <p class="font-medium">-></p>
                        <p class="text-green-300 p-1 rounded font-mono">
                            {{ $value['new'] ?? '' }}
                        </p>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
            @endif
        </div>
    </div>
</div>