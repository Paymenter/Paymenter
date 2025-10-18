@php
// So we get both a $record and $children passed, we need to fetch audits for all

$audits = $record->audits()->orderBy('created_at', 'desc')->get();

foreach ($children as $child) {
$record->{$child}()->each(function ($childRecord) use (&$audits) {
$audits = $audits->merge($childRecord->audits()->orderBy('created_at', 'desc')->get());
});
}
$audits = $audits->sortByDesc('created_at')->sortByDesc('id');

@endphp
<div class="flex flex-col h-full space-y-4">
    @foreach ($audits as $audit)
    <div
        class="fi-in-repeatable-item block rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
        <div class="flex gap-x-3">
            <div class="flex-grow space-y-2 pt-[6px]">
                <div class="flex gap-x-2 items-center justify-between">
                    <div class="flex gap-x-2 items-center">
                        <div class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ $audit->user?->name ?? 'System' }}
                            @if($audit->event == 'extension_action' && isset($audit->new_values['action']))
                                <!-- action name for extension actions -->
                                {{ str_replace('_', ' ', $audit->new_values['action']) }}
                            @else
                            {{ $audit->event }}
                            @endif {{ class_basename($audit->auditable_type) }}
                            (#{{ $audit->auditable_id }})
                        </div>
                    </div>
                    <div class="text-xs text-gray-950 dark:text-white text-nowrap">
                        @if($audit->tags && $audit->tags == 'admin')
                            <span class="text-green-500" title="Action executed by admin">
                                <x-ri-shield-user-line class="inline-block size-4" />
                            </span>
                        @endif
                        {{ $audit->created_at->format('Y-m-d H:i:s') }}
                    </div>
                </div>
                <div class="flex-grow space-y-1">
                    @if($audit->event == 'created')
                    <div class="text-sm text-gray-950 dark:text-white">
                        @if(count($audit->getModified()) === 0)
                        <span class="text-xs text-gray-950 dark:text-white">
                            No changes
                        </span>
                        @else
                        @foreach ($audit->getModified() as $key => $value)
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
                    @elseif($audit->event !== 'extension_action')
                    <div class="text-sm text-gray-950 dark:text-white">
                        @if(count($audit->getModified()) === 0)
                        <span class="text-xs text-gray-950 dark:text-white">
                            No changes
                        </span>
                        @else
                        @foreach ($audit->getModified() as $key => $value)
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
    </div>
    @endforeach

</div>