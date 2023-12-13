<x-admin-layout>
    <x-slot name="title">
        {{ __('Settings') }}
    </x-slot>
    <div id="tabs">
        <div class="flex flex-row overflow-x-auto lg:flex-wrap lg:space-x-1">
            @foreach ($tabs as $tab)
                <div class="flex-none tabs">
                    <a href="#{{ str_replace('admin.settings.settings.', '', $tab) }}"
                        class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-y-transparent hover:border-logo hover:text-logo"
                        id="{{ str_replace('admin.settings.settings.', '', $tab) }}">
                        {{ __(str_replace('admin.settings.settings.', '', $tab)) }}
                    </a>
                </div>
            @endforeach
        </div>
        <!-- Tab panes -->
        <div class="tab-content">
            @foreach ($tabs as $tab)
                @include($tab)
            @endforeach
        </div>
        <br>
    </div>
    <script>
        $(document).ready(function() {
            var hash = $(window.location.hash);
            if (hash[0]) {
                $('.tabs a').removeClass('border-logo');
                $('.tabs a').addClass('border-y-transparent');
                $(hash[0]).removeClass('border-y-transparent');
                $(hash[0]).addClass('border-logo');
                $('.tab-content > div').addClass('hidden');
                $('#tab-' + hash[0].id).removeClass('hidden');
            } else {
                $('.tabs a').removeClass('border-logo');
                $('.tabs a').addClass('border-y-transparent');
                $('.tabs a:first').removeClass('border-y-transparent');
                $('.tabs a:first').addClass('border-logo');
                $('.tab-content > div').addClass('hidden');
                $('.tab-content > div:first').removeClass('hidden');
            }

            // On click on tab
            $('.tabs a').click(function() {
                $('.tabs a').removeClass('border-logo');
                $('.tabs a').addClass('border-y-transparent');
                $(this).removeClass('border-y-transparent');
                $(this).addClass('border-logo');
                $('.tab-content > div').addClass('hidden');
                $('#tab-' + this.id).removeClass('hidden');
            });
        });

        function copyToClipboard(text) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(text).select();
            document.execCommand("copy");
            $temp.remove();

            // Display on the left side of the screen
            var message = "Copied to clipboard";
            document.getElementById("message").innerHTML = message;
            var x = document.getElementById("toast-success");
            x.classList.remove("hidden");
            setTimeout(function() {
                x.classList.add("hidden");
            }, 2500);
        }
    </script>
    <div id="toast-success"
        class="flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 absolute bottom-5 left-5 hidden"
        role="alert">
        <div
            class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ml-3 text-sm font-normal" id="message"></div>
        <button type="button"
            class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
            data-dismiss-target="#toast-success" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
</x-admin-layout>
