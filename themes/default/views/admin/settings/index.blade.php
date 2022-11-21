<x-admin-layout>
    <x-slot name="title">
        {{ __('Settings') }}
    </x-slot>
    <x-success class="mb-4" />

    <div class="container h-full px-6 py-10 mx-auto">
        <div class="w-full h-full rounded ">
            <div class="p-6 mx-auto bg-white border-b border-gray-200 shadow-xl max-w-7xl sm:px-6 lg:px-8 dark:bg-darkmode2 dark:border-darkmode"
                id="tabs">
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
                <br>
            </div>
        </div>
    </div>
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
    </script>

</x-admin-layout>
