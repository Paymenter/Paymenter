<x-admin-layout title="{{ __('Email Templates') }}">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
    <div class="w-full h-full rounded mb-4">
        <div class="px-6 mx-auto">
            <div class="flex flex-row overflow-x-auto lg:flex-wrap lg:space-x-1">
                <div class="flex-none">
                    <a href="{{ route('admin.email') }}"
                       class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton hover:border-logo hover:text-logo @if (request()->routeIs('admin.email')) border-logo @else border-y-transparent @endif">
                        {{ __('Email Logs') }}
                    </a>
                </div>
                <div class="flex-none">
                    <a href="{{ route('admin.email.templates') }}"
                       class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton hover:border-logo hover:text-logo @if (request()->routeIs('admin.email.templates*')) border-logo @else border-y-transparent @endif">
                        {{ __('Templates') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/marked@3.0.4/marked.min.js"></script>
    <form method="POST" enctype="multipart/form-data" id="form" class="mb-14" action="{{ route('admin.email.templates.update') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 m-4">
            <div class="flex flex- justify-between col-span-2 mb-2">
                <h2 class="col-span-1 md:col-span-2 text-2xl font-semibold text-gray-900 dark:text-darkmodetext">{{ __('Email Templates') }}</h2>
                <select onchange="changeTemplate()" class="bg-secondary-200 rounded-md border-0 ring-0 focus:ring-2 focus:ring-secondary-300 transition-all ease-in-out" name="template_name" id="template_name">
                    <optgroup label="Invoices">
                        <option value="invoice_unpaid" @if(old('template_name')??session('updatedTemplate') === 'invoice_unpaid') selected @endif>Unpaid invoice</option>
                        <option value="invoice_new" @if(old('template_name')??session('updatedTemplate') === 'invoice_new') selected @endif>New invoice</option>
                    </optgroup>
                    <optgroup label="Tickets">
                        <option value="ticket_new" @if(old('template_name')??session('updatedTemplate') === 'ticket_new') selected @endif>New ticket</option>
                        <option value="ticket_reply" @if(old('template_name')??session('updatedTemplate') === 'ticket_reply') selected @endif>New reply</option>
                    </optgroup>
                </select>
            </div>

            <div class="relative group md:col-span-2 col-span-1 grid grid-cols-12 gap-x-3 w-full h-[95%]">
                <div class="col-span-6">
                    <textarea
                        name="template"
                        class="form-input p-4 text-md w-full h-full @error('template') is-invalid @enderror"
                        placeholder=""
                        id="template"
                        onchange="updateTemplatePreview()"
                    ></textarea>
                    <div class="font-light text-red-500">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                </div>
                <div class="relative w-full aspect-w-16 col-span-6 dark:bg-darkmode/50 rounded-md overflow-y-auto">
                    <div class="font-sans antialiased rounded-md">

                        <div class="wrapper">
                            <div class="text-center header py-6">
                                <a class="inline-block font-bold text-gray-700 dark:text-gray-300 text-lg no-underline">
                                    <img src="/images/1693228682.png" alt="" width="60px" height="auto" class="inline align-middle">
                                    <span class="inline align-middle">Paymenter</span>
                                </a>
                            </div>


                            <div class="body text-slate-500 dark:text-slate-400 break-all">
                                <div class="inner-body bg-white dark:bg-secondary-100 rounded shadow-sm mx-auto p-7 max-w-lg" id="email-content">

                                </div>
                            </div>
                            <p class="text-xs my-10 text-slate-400 text-center">&copy; 2023 Paymenter. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const templateInput = document.getElementById("template");
                    const previewContainer = document.querySelector("#email-content");
                    const select = document.getElementById("template_name");
                    let firstSelectedOption = select.options[select.selectedIndex].value;

                    const invoice_unpaid = `{{ config('settings::invoice_unpaid_email_template') }}`
                    const invoice_new = `{{ config('settings::invoice_new_email_template') }}`
                    const ticket_new = `{{ config('settings::ticket_new_email_template') }}`
                    const ticket_reply = `{{ config('settings::ticket_reply_email_template') }}`

                    let actualTemplate = invoice_unpaid;
                    templateInput.value = actualTemplate;

                    const data = {
                        remove_unpaid_order_after: "{{ config('settings::remove_unpaid_order_after')??7 }}",
                        total: "00.00",
                        currency: "{{ config('settings::currency') }}",
                    }

                    const specialTags = {
                        table: '<div class="table w-full mt-3"><table class="w-full mb-2"><thead><tr><th class="text-left border-b-[1px] dark:border-slate-400 border-gray-200 pb-2">{{ __('Product') }}</th><th class="text-left border-b-[1px] dark:border-slate-400 border-gray-200 pb-2">{{ __('Price') }}</th></tr></thead><tbody><tr><td class="text-left text-sm py-2">Test (2023-08-22 - 2023-09-22)</td><td class="text-left text-sm py-2">' + data.total + ' ' + data.currency + '</td></tr></tbody></table></div>',
                        remove_unpaid_order_after: data.remove_unpaid_order_after,
                        currency: data.currency,
                        total: data.total,
                    };

                    const contentTags = {
                        button: '<div class="action text-center"><a class="button button-primary hover:cursor-pointer inline-block bg-blue-600 text-white py-2 px-6 rounded-lg shadow-lg hover:bg-blue-700" target="_blank" rel="noopener">{content}</a></div>',
                        center: '<div class="text-center">{content}</div>',
                        title: '<span class="text-lg font-bold text-gray-700 dark:text-slate-300 text-3xl">{content}</span>',
                    }

                    function replaceSpecialTags(content) {
                        const contentTagsReplace = content.replace(/\{(\w+)\}(.*?)\{\/\1\}/gs, (match, tag, text) => {
                            for (const contentTag in contentTags) {
                                if (contentTag === tag) {
                                    return contentTags[tag].replace("{content}", text);
                                }
                            }
                        });

                        const LineBreaksReplace = contentTagsReplace.replace(/\n/g, '<br>\n');

                        return LineBreaksReplace.replace(/\{(\w+)\/}/g, (match, tag, text) => {
                            for (const specialTag in specialTags) {
                                if (specialTag === tag) {
                                    return specialTags[tag];
                                }
                            }
                            return '<span class="text-red-500">' + match + '</span>';
                        });
                    }

                    function updateTemplatePreview() {
                        const markdownContent = marked.parse(templateInput.value);
                        const replacedContent = replaceSpecialTags(markdownContent);
                        previewContainer.innerHTML = replacedContent;
                    }

                    templateInput.addEventListener("input", updateTemplatePreview);
                    select.addEventListener("change", changeTemplate);

                    updateTemplatePreview();

                    function changeTemplate() {
                        const selectedOption = select.options[select.selectedIndex].value;
                        let newTemplate = "";
                        switch (selectedOption) {
                            case "invoice_unpaid":
                                newTemplate = invoice_unpaid;
                                updateTemplatePreview();
                                break;
                            case "invoice_new":
                                newTemplate = invoice_new;
                                updateTemplatePreview();
                                break;
                            case "ticket_new":
                                newTemplate = ticket_new;
                                updateTemplatePreview();
                                break;
                            case "ticket_reply":
                                newTemplate = ticket_reply;
                                updateTemplatePreview();
                                break;
                            default:
                                newTemplate = invoice_unpaid;
                                updateTemplatePreview();
                                break;
                        }
                        if (actualTemplate !== templateInput.value) {
                            Swal.fire({
                                title: '{{ __('Do you want to save the changes?') }}',
                                showDenyButton: true,
                                showCancelButton: true,
                                confirmButtonText: '{{ __('Save') }}',
                                denyButtonText: `{{ __("Don\'t save") }}`,
                                cancelButtonText: '{{ __("Cancel") }}',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const form = document.getElementById("form");
                                    select.value = firstSelectedOption;
                                    form.submit();
                                } else if (result.isDenied) {
                                    // Discard the changes
                                    templateInput.value = newTemplate;
                                    actualTemplate = newTemplate;
                                    firstSelectedOption = selectedOption;
                                    updateTemplatePreview();
                                } else {
                                    select.value = firstSelectedOption;
                                    updateTemplatePreview();
                                }
                            });
                        } else {
                            templateInput.value = newTemplate;
                            actualTemplate = newTemplate;
                            firstSelectedOption = selectedOption;
                            updateTemplatePreview();
                        }
                    }
                    changeTemplate();

                });
            </script>
        </div>
        <button class="float-right form-submit">{{ __('Submit') }}</button>
    </form>
    <button onclick="document.getElementById('template').value += '{title}Title Content{/title}';">Add title</button>
</x-admin-layout>
