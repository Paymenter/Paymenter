@php
    $audits = $record->audits;
    // Also get the child audits
    if (get_class($record) === 'App\Models\Product') {
        $record->plans->each(function ($plan) use (&$audits) {
            $audits = $audits->merge($plan->audits->map(function ($audit) use ($plan) {
                $audit->event = 'Plan ' . $plan->name . ' ' . $audit->event;
                return $audit;
            }));
            $plan->prices->each(function ($price) use (&$audits, $plan) {
                $audits = $audits->merge($price->audits->map(function ($audit) use ($price, $plan) {
                    $audit->event = 'Price from Plan ' . $plan->name . ' ' . $audit->event;
                    return $audit;
                }));
            });
        });
    }

    // sort
    $audits = $audits->sortByDesc('created_at');
@endphp

<div class="flex flex-col h-full space-y-4">

    <x-filament::grid class="gap-4">

        @foreach ($audits as $audit)
            <div
                class="fi-in-repeatable-item block rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                <div class="flex gap-x-3">
                    <div class="flex-grow space-y-2 pt-[6px]">
                        <div class="flex gap-x-2 items-center justify-between">
                            <div class="flex gap-x-2 items-center">
                                <div class="text-sm font-medium text-gray-950 dark:text-white">
                                    {{ $audit->user->name }}
                                </div>
                                <div class="text-xs text-gray-950 dark:text-white">
                                    {{ $audit->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="text-xs text-gray-950 dark:text-white">
                                {{ $audit->created_at->format('Y-m-d H:i:s') }}
                            </div>
                        </div>
                        <div class="text-sm text-gray-950 dark:text-white">
                            {{ $audit->event }}

                            @if(count($audit->getModified()) === 0)
                                <span class="text-xs text-gray-950 dark:text-white">
                                    (No changes)
                                </span>
                                @else
                            @foreach ($audit->getModified() as $key => $value)
                                <div class="my-2 p-2 bg-background-secondary rounded">
                                    <span class="font-semibold">{{ $key }}</span> changed from
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-red-300 p-1 rounded">
                                            <code>{{ $value['old'] ?? '' }}</code>
                                        </span>
                                        <span class="font-medium">to</span>
                                        <span class="text-green-300 p-1 rounded">
                                            <code>{{ $value['new'] ?? '' }}</code>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </x-filament::grid>
</div>
