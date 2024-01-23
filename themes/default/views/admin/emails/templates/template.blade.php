<x-admin-layout>
    <div class="w-full h-full rounded mb-4">
        <div class="mx-auto">
            <div class="flex flex-row overflow-x-auto lg:flex-wrap lg:space-x-1">
                <div class="flex-none">
                    <a href="{{ route('admin.email') }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton hover:border-logo hover:text-logo @if (request()->routeIs('admin.email')) border-logo @else border-y-transparent @endif">
                        {{ __('Email Logs') }}
                    </a>
                </div>
                <div class="flex-none">
                    <a href="{{ route('admin.email.templates') }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton hover:border-logo hover:text-logo @if (request()->routeIs('admin.email.templates')) border-logo @else border-y-transparent @endif">
                        {{ __('Email Templates') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <h1 class="text-3xl font-semibold text-secondary-900 dark:text-darkmodetext">
        {{ __('Editing template') }} {{ class_basename($template->mailable) }}</h1>

    <form action="{{ route('admin.email.template.update', $template->id) }}" method="POST" class="pb-8 h-full">
        @csrf
        <x-input type="text" name="subject" class="w-full mt-4" value="{{ $template->subject }}" label="Subject" />
        <x-input type="textarea" id="editor" class="w-full mt-4" rows="20" label="HTML Template (Markdown supported)"
            name="html_template">{{ $template->html_template }}</x-input>
        <button class="button button-primary float-right">{{ __('Save') }}</button>
    </form>
</x-admin-layout>
