<x-admin-layout title="Emails">
    <h1 class="text-3xl font-semibold text-secondary-900">{{ __('Sended Emails') }}</h1>
    <div class="flex flex-col gap-4 mt-4" id="email" data-accordion="collapse">
        @foreach ($emails as $email)
            <h2 id="email-heading-{{ $email->id }}">
                <button type="button"
                    class="flex items-center justify-between p-5 w-full h-10 rounded-md bg-secondary-200 dark:bg-secondary-50 text-gray-900 dark:text-white"
                    data-accordion-target="#email-body-{{ $email->id }}" aria-expanded="true"
                    aria-controls="email-body-{{ $email->id }}">
                    <span class="w-full text-left"> {{ $email->subject }} - {{ $email->created_at }} - to:
                        {{ $email->user->name }} </span>
                    <span class="w-full">
                        @if ($email->success)
                            <span
                                class="bg-green-500 text-white rounded-full px-3 py-1 text-xs font-bold">{{ __('Sent') }}</span>
                        @else
                            <span
                                class="bg-red-500 text-white rounded-full px-3 py-1 text-xs font-bold">{{ __('Failed') }}</span>
                        @endif
                    </span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="email-body-{{ $email->id }}" class="hidden"
                aria-labelledby="email-heading-{{ $email->id }}">
                <div class="w-full rounded-md p-2">
                    @if (!$email->success)
                        <div class="bg-red-100 border border-red-500 text-red-700 px-4 py-3 rounded relative"
                            role="alert">
                            <!-- Error is a string with the error message and stacktrace -->
                            @php
                                $error = $email->errors;
                                $error = explode("\n", $error);
                            @endphp
                            @foreach ($error as $key => $value)
                                @if ($key > 0)
                                    <span class="block sm:inline">{{ $value }}</span>
                                @else 
                                    <strong class="font-bold">{{ $value }}</strong>
                                @endif
                                <br />
                            @endforeach
                        </div>
                    @else
                        <iframe srcdoc="{{ $email->body }}" frameborder="0" class="w-full" style="min-height: 500px;">
                        </iframe>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex justify-end mt-4">
        <x-pagination :paginator="$emails" />
    </div>
</x-admin-layout>
