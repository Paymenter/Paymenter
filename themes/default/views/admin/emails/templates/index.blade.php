<x-admin-layout title="Email templates">
    <h1 class="text-3xl font-semibold text-secondary-900 dark:text-darkmodetext">{{ __('Email Templates') }}</h1>
    <div class="mt-10">
        <table class="min-w-full mt-4">
            <thead class="bg-gray-50 dark:bg-secondary-200 text-left">
                <tr>
                    <th class="px-1 pl-3 py-3">
                        {{ __('Mailable') }}
                    </th>
                    <th class="px-1 pr-2 py-3">
                        {{ __('Action') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($templates as $template)
                    <tr id="{{ $template->id }}">
                        <td class="py-2 px-4">
                            {{ class_basename($template->mailable) }}</td>
                        <td class="py-2">
                            <a href="{{ route('admin.email.template', $template->id) }}">
                                <button class="button button-primary">
                                    <i class="ri-pencil-line"></i> {{ __('Edit') }}
                                </button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
