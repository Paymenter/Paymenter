<x-admin-layout title="{{ ucfirst($level) }} Logs">
    <h1 class="text-3xl font-emibold text-secondary-900 dark:text-darkmodetext mb-1">{{ ucfirst($level) }} Logs</h1>
    <div class="w-full h-full rounded mb-4">
        <div class="mx-auto">
            <div class="flex flex-row overflow-x-auto lg:flex-wrap lg:space-x-1">
                <div class="flex-none">
                    <a href="{{ route('admin.logs') }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton hover:border-logo hover:text-logo @if (request()->routeIs('admin.logs') && !request()->input('level')) border-logo @else border-y-transparent @endif">
                        {{ __('Error Logs') }}
                    </a>
                </div>
                <div class="flex-none">
                    <a href="{{ route('admin.logs') . '?level=debug' }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton hover:border-logo hover:text-logo @if (request()->routeIs('admin.logs') && request()->input('level') == 'debug') border-logo @else border-y-transparent @endif">
                        {{ __('Debug logs') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <livewire:admin.logs.upload />
    @if (request()->input('level') == 'debug')
        @if (config('settings::debug_logs_enabled') == 'true')
            <form action="{{ route('admin.logs.debug') }}" method="POST">
                @csrf
                <input type="hidden" name="disable" value="true">
                <button type="submit" class="button button-secondary">
                    {{ __('Disable Debug Logs') }}
                </button>
            </form>
        @else
            <form action="{{ route('admin.logs.debug') }}" method="POST">
                @csrf
                <input type="hidden" name="enable" value="true">
                <button type="submit" class="button button-secondary">
                    {{ __('Enable Debug Logs') }}
                </button>
            </form>
        @endif
    @endif
    <div class="flex flex-col gap-4 mt-4" id="logs" data-accordion="collapse">
        @foreach ($logs as $log)
            <div class="flex flex-col gap-1">
                <h2 id="log-heading-{{ $log->id }}">
                    <button type="button"
                        class="flex items-center justify-between p-5 w-full h-auto rounded-md bg-secondary-200 dark:bg-secondary-50 text-gray-900 dark:text-white"
                        data-accordion-target="#log-body-{{ $log->id }}" aria-expanded="true"
                        aria-controls="log-body-{{ $log->id }}">
                        <span class="w-full text-left transition-all ease-in-out "> {{ $log->message }} -
                            {{ $log->created_at }} </span>
                        <svg data-accordion-icon class="w-3 h-3 rotate-180 transition-all ease-in-out shrink-0"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5 5 1 1 5" />
                        </svg>
                    </button>
                </h2>
                <div id="log-body-{{ $log->id }}" class="hidden transition-all ease-in-out"
                    aria-labelledby="log-heading-{{ $log->id }}">
                    <div
                        class="prose dark:prose-invert bg-gray-100 dark:bg-gray-800 rounded-md h-fit p-3 pt-0 max-w-full">
                        {!! nl2br(e($log->data)) !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex justify-end mt-4">
        <x-pagination :paginator="$logs" />
    </div>
</x-admin-layout>
