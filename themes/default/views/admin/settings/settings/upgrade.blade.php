<div class="hidden mt-3" id="tab-upgrade">
    @if (config('app.version') == 'development')
        <div class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext ">
            {{ __('You are running a development version') }}
        </div>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative m-4 ml-6" role="alert">
            <strong class="font-bold">{{ __('This version is not supported for production use. It may contain various errors and some functions may not work.') }}</strong>
            <span class="block sm:inline">{{ __('Please use the latest stable version.') }}</span>
        </div>
    @else
        <div class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('You are running version') }}
            {{ config('app.version') }}</div>
        <div class="m-4 ml-6 text-xl text-gray-900 dark:text-darkmodetext ">{{ __('Latest version') }}
            <span id="latest-version"></span>
        </div>
    @endif

    <script>
        document.getElementById('latest-version').innerHTML = '...';
        fetch('https://api.github.com/repos/Paymenter/Paymenter/releases/latest')
            .then(response => response.json())
            .then(data => {
                if(data.tag_name == 'v{{ config('app.version') }}') {
                    document.getElementById('latest-version').innerHTML = data.tag_name + ' ({{ __('Latest') }})';
                } else {
                    document.getElementById('latest-version').innerHTML = data.tag_name + ' ({{ __('Update available') }})' + '<br><a href="https://paymenter.org/docs/how-to-update" class="text-blue-500 hover:text-blue-700">{{ __('Upgrade') }}</a>';
                }
            });
    </script>
</div>
