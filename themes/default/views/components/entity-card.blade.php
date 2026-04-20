@props(['href', 'statusClass' => ''])

<a href="{{ $href }}" wire:navigate>
    <div class="bg-background-secondary hover:bg-background-secondary/80 border border-neutral p-4 rounded-lg mb-4">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-3">
                <div class="bg-secondary/10 p-2 rounded-lg">
                    {{ $icon }}
                </div>
                {{ $heading }}
            </div>
            <div class="size-5 rounded-md p-0.5 {{ $statusClass }}">
                {{ $status }}
            </div>
        </div>
        @if(!$detail->isEmpty())
            {{ $detail }}
        @endif
    </div>
</a>
