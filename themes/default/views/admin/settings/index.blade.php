<x-admin-layout>
    <x-slot name="title">
        {{ __('Settings') }}
    </x-slot>

    <div class="container h-full px-6 py-10 mx-auto">
        <div class="w-full h-full rounded">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8" id="tabs">
                <h1 class="text-xl text-gray-500 dark:text-darkmodetext">W.I.P.!</h1>
                    <br>
                <div class="flex flex-row overflow-x-auto lg:flex-wrap lg:space-x-1">
                    @foreach ($tabs as $tab)
                        <div class="flex-none tabs">
                            <a href="#{{ str_replace('admin.settings.settings.', '', $tab) }}"
                                class="inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-y-transparent hover:border-logo hover:text-logo"
                                id="{{ str_replace('admin.settings.settings.', '', $tab) }}">
                                {{ str_replace('admin.settings.settings.', '', $tab) }}
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

            </div>
        </div>
    </div>
    </div>
    <script>
        $(document).ready(function() {
            var hash = window.location.hash;
            if (hash) {
                $(hash).removeClass();
                $(hash).addClass('inline-flex justify-center w-full p-4 px-2 py-2 text-xs font-bold text-gray-900 uppercase border-b-2 dark:text-darkmodetext dark:hover:bg-darkbutton border-logo hover:border-logo hover:text-logo tabs');
            } else {
                $('.nav-tabs a:first').addClass('active');
                $('.tab-content div:first').addClass('active');
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
    </script>

</x-admin-layout>
